<?php

namespace App\Livewire;

use App\Models\Conversation;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ChatList extends Component
{
     public $type = 'all';
    public $conversation;
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

        $conversations = Conversation::where(function ($query) use ($user) {
            $query->where('sender_id', $user->id)
                ->orWhere('receiver_id', $user->id);
        })
            ->when($this->client_id, function ($query) {
                $query->where('client_id', $this->client_id);
            })
            ->latest('updated_at')
            ->get();

        return view('livewire.chat-list', [
            'conversations' => $conversations,
        ]);
    }
}
