<?php

namespace App\Livewire;

use App\Models\ManagerClientChat;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ManagerChatList extends Component
{
    public $chats = [];
    public $search = '';

    protected $listeners = ['messageSent' => 'loadChats'];

    public function mount()
    {
        $this->loadChats();
    }

    public function loadChats()
    {
        $user = Auth::user();
        
        $query = ManagerClientChat::where('sales_rep_id', $user->id)
            ->orWhere('manager_id', $user->id);

        if ($this->search) {
            $query->whereHas('client', function ($q) {
                $q->where('company_name', 'like', '%' . $this->search . '%');
            });
        }

        $this->chats = $query->with(['client', 'salesRep', 'manager', 'latestMessage'])
            ->get()
            ->map(function ($chat) use ($user) {
                $chat->unread_count = $chat->unreadMessagesFor($user->id);
                return $chat;
            })
            ->sortByDesc(function ($chat) {
                return $chat->latestMessage?->created_at ?? $chat->created_at;
            });
    }

    public function updatedSearch()
    {
        $this->loadChats();
    }

    public function render()
    {
        return view('livewire.manager-chat-list');
    }
}
