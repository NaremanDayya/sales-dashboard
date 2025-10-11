<?php

namespace App\Livewire;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ChatList extends Component
{
    public $type = 'all';
    public $conversation = null;
    public $selectedConversation;
    public $client_id;
    public $perPage = 20;
    public $hasMore = false;
    public $loadedCount = 0;
    public $loading = false;

    protected $listeners = [
        'refresh' => 'refresh',
        'loadMore' => 'loadMore'
    ];

    public function mount($client_id = null, $selectedConversation = null)
    {
        $this->client_id = $client_id;
        $this->selectedConversation = $selectedConversation;
    }

    public function refresh()
    {
        $this->perPage = 20;
        $this->loadedCount = 0;
        $this->hasMore = false;
        $this->loading = false;
    }

    public function loadMore()
    {
        if ($this->loading || !$this->hasMore) {
            return;
        }

        $this->loading = true;
        $this->perPage += 8;
    }

    public function render()
    {
        $user = Auth::user();

        $conversations = Conversation::with([
            'client:id,sales_rep_id,company_name,company_logo',
            'client.salesRep:id,name',
        ])
            ->where(function ($query) use ($user) {
                $query->where('sender_id', $user->id)
                    ->orWhere('receiver_id', $user->id);
            })
            ->when($this->client_id, function ($query) {
                $query->where('client_id', $this->client_id);
            })
            ->orderBy('updated_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit($this->perPage)
            ->get();

        $this->loadedCount = $conversations->count();

        // Manually get latest message data for each conversation
        if ($conversations->isNotEmpty()) {
            $conversationIds = $conversations->pluck('id');

            // Get the latest message for each conversation
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

            // Attach latest message data to conversations
            $conversations->each(function ($conversation) use ($latestMessages, $user) {
                $latestMessage = $latestMessages->get($conversation->id);

                // Add latest message data as custom attributes
                $conversation->latest_message_text = $latestMessage ? $latestMessage->message : '';
                $conversation->latest_message_time = $latestMessage ? $latestMessage->created_at : null;
                $conversation->latest_message_sender_id = $latestMessage ? $latestMessage->sender_id : null;

                // Calculate unread count using your model method
                $conversation->unread_messages_count = $conversation->unreadMessagesCount();
            });
        }

        // Check if there are more conversations to load
        $totalConversations = Conversation::where(function ($query) use ($user) {
            $query->where('sender_id', $user->id)
                ->orWhere('receiver_id', $user->id);
        })
            ->when($this->client_id, function ($query) {
                $query->where('client_id', $this->client_id);
            })
            ->count();

        $this->hasMore = $this->loadedCount < $totalConversations;
        $this->loading = false;

        return view('livewire.chat-list', [
            'conversations' => $conversations,
            'selectedConversation' => $this->selectedConversation,
        ]);
    }
}
