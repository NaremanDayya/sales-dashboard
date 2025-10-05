<?php

namespace App\Livewire;

use App\Models\Conversation;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ChatList extends Component
{
    public $type = 'all';
    public $conversation = null;
    public $selectedConversation;
    public $client_id;

    protected $listeners = [
        'refresh' => 'refresh'
    ];

    public function mount($client_id = null)
    {
        $this->client_id = $client_id;
    }

    public function refresh()
    {
        $this->render();
    }

    public function render()
    {
        $user = Auth::user();

        $conversations = Conversation::with([
            // Eager load all necessary relationships
            'client.salesRep',
            'latestMessage.sender',
            'latestMessage.receiver',
            'messages.sender', // Prevent N+1 in views
            'messages.receiver'
        ])
            ->where(function ($query) use ($user) {
                $query->where('sender_id', $user->id)
                    ->orWhere('receiver_id', $user->id);
            })
            ->when($this->client_id, function ($query) {
                $query->where('client_id', $this->client_id);
            })
            ->withCount([
                'messages as unread_messages_count' => function($query) use ($user) {
                    $query->where('receiver_id', $user->id)
                        ->whereNull('read_at');
                }
            ])
            ->orderByRaw("CASE WHEN unread_messages_count > 0 THEN 0 ELSE 1 END")
            ->orderBy('updated_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('livewire.chat-list', [
            'conversations' => $conversations,
        ]);
    }
}
