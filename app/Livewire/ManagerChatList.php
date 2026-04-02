<?php

namespace App\Livewire;

use App\Models\ManagerClientChat;
use App\Models\ManagerChatMessage;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ManagerChatList extends Component
{
    public $selectedChat;
    public $search = '';
    public $perPage = 30;
    public $hasMore = true;
    public $loading = false;
    public $page = 1;
    public $lastLoadTime = 0;
    public $allChats;
    private $lastBatchHasMore = false;
    public $filter = 'newest';
    public $dateFilter = null;

    protected $listeners = [
        'chatUpdated' => 'handleChatUpdate',
        'newMessageReceived' => 'handleNewMessage',
        'refreshChatList' => 'refresh',
    ];

    public function mount($selectedChatId = null)
    {
        if ($selectedChatId) {
            $this->selectedChat = ManagerClientChat::find($selectedChatId);
        }
        $this->loadInitialChats();
    }

    public function loadInitialChats()
    {
        $this->allChats = $this->getChats(1);
        $this->hasMore = $this->lastBatchHasMore;
    }

    public function loadMore()
    {
        $now = microtime(true);
        if ($this->loading || !$this->hasMore || ($now - $this->lastLoadTime) < 0.3) {
            return;
        }

        $this->loading = true;
        $this->lastLoadTime = $now;
        $this->page++;

        try {
            $additionalChats = $this->getChats($this->page);
            $this->hasMore = $this->lastBatchHasMore;

            $this->allChats = $this->allChats
                ->concat($additionalChats)
                ->unique('id')
                ->values();
        } catch (\Exception $e) {
            \Log::error('ManagerChatList loadMore error: ' . $e->getMessage());
            $this->hasMore = false;
        } finally {
            $this->loading = false;
        }
    }

    public function handleChatUpdate($chatId)
    {
        $this->refresh();
    }

    public function handleNewMessage($data)
    {
        $this->refresh();
    }

    public function refresh()
    {
        $this->page = 1;
        $this->hasMore = true;
        $this->loading = false;
        $this->loadInitialChats();
    }

    public function updatedFilter()
    {
        $this->refresh();
    }

    public function updatedDateFilter()
    {
        $this->refresh();
    }

    public function updatedSearch()
    {
        $this->refresh();
    }

    private function getChats($page = 1)
    {
        $user = Auth::user();
        $offset = ($page - 1) * $this->perPage;

        $query = ManagerClientChat::with([
            'client:id,sales_rep_id,company_name,company_logo',
            'client.salesRep:id,name',
            'salesRep:id,name',
            'manager:id,name',
        ])
            ->where(function ($query) use ($user) {
                $query->where('sales_rep_id', $user->id)
                    ->orWhere('manager_id', $user->id);
            })
            ->when($this->search, function ($query) {
                $query->whereHas('client', function ($q) {
                    $q->where('company_name', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->dateFilter, function ($query) {
                $query->whereHas('messages', function ($q) {
                    $q->whereDate('created_at', $this->dateFilter);
                });
            })
            ->addSelect([
                'latest_message_created_at' => ManagerChatMessage::selectRaw('MAX(created_at)')
                    ->whereColumn('manager_client_chat_id', 'manager_client_chats.id')
            ]);

        $query->withCount(['messages as unread_count' => function ($sub) use ($user) {
            $sub->whereNull('read_at')
                ->where('sender_id', '!=', $user->id);
        }]);

        $query->when($this->filter === 'unread', function ($q) {
            $q->having('unread_count', '>', 0);
        });

        $query->when($this->filter === 'read', function ($q) {
            $q->having('unread_count', '=', 0);
        });

        if ($this->filter === 'oldest') {
            $query->orderBy('unread_count', 'desc')
                  ->orderBy('latest_message_created_at', 'asc')
                  ->orderBy('created_at', 'asc');
        } else {
            $query->orderBy('unread_count', 'desc')
                  ->orderBy('latest_message_created_at', 'desc')
                  ->orderBy('created_at', 'desc');
        }

        $chats = $query
            ->skip($offset)
            ->take($this->perPage + 1)
            ->get();

        $this->lastBatchHasMore = $chats->count() > $this->perPage;
        if ($this->lastBatchHasMore) {
            $chats = $chats->slice(0, $this->perPage)->values();
        }

        return $this->enhanceChatsWithMessages($chats);
    }

    private function enhanceChatsWithMessages($chats)
    {
        if ($chats->isEmpty()) {
            return $chats;
        }

        $chatIds = $chats->pluck('id');

        $latestMessages = ManagerChatMessage::whereIn('manager_client_chat_id', $chatIds)
            ->whereIn('id', function($query) use ($chatIds) {
                $query->select(\DB::raw('MAX(id)'))
                    ->from('manager_chat_messages')
                    ->whereIn('manager_client_chat_id', $chatIds)
                    ->groupBy('manager_client_chat_id');
            })
            ->select('id', 'manager_client_chat_id', 'message', 'created_at', 'sender_id', 'read_at')
            ->orderBy('id', 'desc')
            ->get()
            ->keyBy('manager_client_chat_id');

        $enhanced = $chats->map(function ($chat) use ($latestMessages, $user) {
            $latestMessage = $latestMessages->get($chat->id);

            $chat->latest_message_text = $latestMessage ? $latestMessage->message : '';
            $chat->latest_message_time = $latestMessage ? $latestMessage->created_at : null;
            $chat->latest_message_sender_id = $latestMessage ? $latestMessage->sender_id : null;
            $chat->is_last_message_read = $latestMessage && $latestMessage->read_at ? true : false;

            if (!isset($chat->unread_count)) {
                $chat->unread_count = $chat->unreadMessagesFor($user->id);
            }

            return (object) $chat->toArray();
        });

        return $enhanced;
    }

    public function render()
    {
        return view('livewire.manager-chat-list', [
            'chats' => $this->allChats ?? collect(),
        ]);
    }
}
