<?php

namespace App\Livewire;

use App\Models\Client;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ChatContainer extends Component
{
    public $selectedConversationId;
    public $client;
    public $initialConversation;

    public function mount(Client $client = null, Conversation $conversation = null)
    {
        $this->client = $client;
        $this->initialConversation = $conversation;
        $this->selectedConversationId = $conversation?->id;

        if ($this->selectedConversationId) {
            $this->markAsRead($this->selectedConversationId);
        }
    }

    public function selectConversation($conversationId)
    {
        $this->selectedConversationId = $conversationId;
        $this->dispatch('conversationSelected', $conversationId);
        $this->markAsRead($conversationId);
    }

    private function markAsRead($conversationId)
    {
        Message::where('conversation_id', $conversationId)
            ->where('receiver_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    public function render()
    {
        return view('livewire.chat-container', [
            'initialConversationId' => $this->selectedConversationId,
            'client' => $this->client,
            'initialConversation' => $this->initialConversation,
        ]);
    }
}

