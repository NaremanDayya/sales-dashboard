<?php

namespace App\Livewire;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ChatList extends Component
{
    public $client;
    public $selectedConversation;
    public $search = '';
    public $perPage = 20;
    public $hasMore = true;
    public $loading = false;
    public $page = 1;
    public $allConversations;
    private $lastBatchHasMore = false;
    public $filter = 'newest';

    protected $listeners = [
        'conversationUpdated' => 'handleConversationUpdate',
        'newMessageReceived' => 'handleNewMessage',
        'refreshChatList' => 'refresh',
        'updateUnreadCount' => 'updateUnreadCount',
    ];

    public function mount($client = null)
    {
        $this->client = $client;
        $this->loadInitialConversations();
    }

    public function loadInitialConversations()
    {
        $this->allConversations = $this->getConversations(1);
        $this->hasMore = $this->lastBatchHasMore;
    }

    public function loadMore()
    {
        if ($this->loading || !$this->hasMore) {
            return;
        }

        $this->loading = true;
        $this->page++;

        $additionalConversations = $this->getConversations($this->page);
        $this->hasMore = $this->lastBatchHasMore;

        // Merge new conversations with existing ones
        $this->allConversations = $this->allConversations
            ->concat($additionalConversations)
            ->unique('id')
            ->values();

        $this->loading = false;

        // Dispatch event to Alpine.js with unread counts
        $this->dispatch('conversationsLoaded',
            conversations: $this->allConversations->map(function($conv) {
                return [
                    'id' => $conv->id,
                    'unread_count' => $conv->unread_count ?? 0
                ];
            })->toArray()
        );
    }

    public function handleConversationUpdate($conversationId)
    {
        $this->refresh();
    }

    public function handleNewMessage($data)
    {
        if (isset($data['conversation_id'])) {
            // Update specific conversation's unread count
            $this->dispatch('updateConversationUnread',
                conversationId: $data['conversation_id'],
                increment: true
            );
        } else {
            $this->refresh();
        }
    }

    public function updateUnreadCount($conversationId, $count)
    {
        $this->dispatch('updateConversationUnread',
            conversationId: $conversationId,
            count: $count
        );
    }

    public function refresh()
    {
        $this->page = 1;
        $this->hasMore = true;
        $this->loading = false;
        $this->loadInitialConversations();

        // Dispatch event to reset Alpine.js store
        $this->dispatch('resetUnreadStore');
    }

    public function updatedFilter()
    {
        $this->refresh();
    }

    private function getConversations($page = 1)
    {
        $user = Auth::user();
        $offset = ($page - 1) * $this->perPage;

        $query = Conversation::with([
            'client:id,sales_rep_id,company_name,company_logo',
            'client.salesRep:id,name',
        ])
            ->where(function ($query) use ($user) {
                $query->where('sender_id', $user->id)
                    ->orWhere('receiver_id', $user->id);
            })
            ->when($this->search, function ($query) {
                $query->whereHas('client', function ($q) {
                    $q->where('company_name', 'like', '%' . $this->search . '%')
                        ->orWhereHas('salesRep', function ($q2) {
                            $q2->where('name', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->addSelect([
                'latest_message_created_at' => Message::selectRaw('MAX(created_at)')
                    ->whereColumn('conversation_id', 'conversations.id')
            ]);

        $query->withCount(['messages as unread_count' => function ($sub) use ($user) {
            $sub->whereNull('read_at')
                ->where('sender_id', '!=', $user->id);
        }]);

        // Apply filter conditions
        $query->when($this->filter === 'unread', function ($q) {
            $q->having('unread_count', '>', 0);
        });

        $query->when($this->filter === 'read', function ($q) {
            $q->having('unread_count', '=', 0);
        });

        // Apply ordering
        if ($this->filter === 'oldest') {
            $query->orderBy('unread_count', 'desc')
                ->orderBy('latest_message_created_at', 'asc')
                ->orderBy('created_at', 'asc');
        } else {
            $query->orderBy('unread_count', 'desc')
                ->orderBy('latest_message_created_at', 'desc')
                ->orderBy('created_at', 'desc');
        }

        $conversations = $query
            ->skip($offset)
            ->take($this->perPage + 1)
            ->get();

        $this->lastBatchHasMore = $conversations->count() > $this->perPage;
        if ($this->lastBatchHasMore) {
            $conversations = $conversations->slice(0, $this->perPage)->values();
        }

        return $this->enhanceConversationsWithMessages($conversations);
    }

    private function enhanceConversationsWithMessages($conversations)
    {
        if ($conversations->isEmpty()) {
            return $conversations;
        }

        $conversationIds = $conversations->pluck('id');

        // Get latest messages
        $latestMessages = Message::whereIn('conversation_id', $conversationIds)
            ->whereIn('id', function($query) use ($conversationIds) {
                $query->select(\DB::raw('MAX(id)'))
                    ->from('messages')
                    ->whereIn('conversation_id', $conversationIds)
                    ->groupBy('conversation_id');
            })
            ->select('id', 'conversation_id', 'message', 'created_at', 'sender_id')
            ->get()
            ->keyBy('conversation_id');

        // Enhance conversations with message data
        $conversations->each(function ($conversation) use ($latestMessages) {
            $latestMessage = $latestMessages->get($conversation->id);

            $conversation->latest_message_text = $latestMessage ? $latestMessage->message : '';
            $conversation->latest_message_time = $latestMessage ? $latestMessage->created_at : null;
            $conversation->latest_message_sender_id = $latestMessage ? $latestMessage->sender_id : null;
            $conversation->receiver_name = $conversation->getReceiver()->name;
            $conversation->is_last_message_read = $conversation->isLastMessageReadByUser();

            if (!isset($conversation->unread_count)) {
                $conversation->unread_count = $conversation->unreadMessagesCount();
            }
        });

        return $conversations;
    }

    public function updatedSearch()
    {
        $this->refresh();
    }

    public function render()
    {
        return view('livewire.chat-list', [
            'conversations' => $this->allConversations ?? collect(),
        ]);
    }
}
