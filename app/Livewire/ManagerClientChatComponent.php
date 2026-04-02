<?php

namespace App\Livewire;

use App\Models\ManagerClientChat;
use App\Models\ManagerChatMessage;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ManagerClientChatComponent extends Component
{
    public $chat;
    public $message = '';
    public $messages = [];

    protected $listeners = ['messageReceived' => 'loadMessages'];

    public function mount(ManagerClientChat $chat)
    {
        $this->chat = $chat;
        $this->authorize('view', $chat);
        $this->loadMessages();
        $this->markMessagesAsRead();
    }

    public function loadMessages()
    {
        $this->messages = $this->chat->messages()
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function sendMessage()
    {
        $this->authorize('sendMessage', $this->chat);

        $this->validate([
            'message' => 'required|string|max:5000',
        ]);

        ManagerChatMessage::create([
            'manager_client_chat_id' => $this->chat->id,
            'sender_id' => Auth::id(),
            'message' => $this->message,
        ]);

        $this->message = '';
        $this->loadMessages();
        $this->dispatch('messageSent');
    }

    public function markMessagesAsRead()
    {
        $this->chat->messages()
            ->where('sender_id', '!=', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    public function render()
    {
        return view('livewire.manager-client-chat-component');
    }
}
