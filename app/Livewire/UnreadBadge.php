<?php
namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Namu\WireChat\Facades\WireChat;

class UnreadBadge extends Component
{

    public ?int $selectedConversationId = null;

    public function getUnreadCount(): int
    {
        $auth = Auth::user();

        if (! $auth) {
            return 0;
        }

        $conversations = $auth->conversations()
            ->with([
                'lastMessage.sendable',
                'participants.participantable',
            ])
            ->latest('updated_at')
            ->take(20) // or adjust as needed
            ->get();

        $unreadCount = collect($conversations)->filter(function ($conv) use ($auth) {
            $lastMessage = $conv->lastMessage;

            return $lastMessage &&
                ! $lastMessage->ownedBy($auth) &&
                ! $conv->readBy($conv->participant($auth)) &&
                $conv->id != $this->selectedConversationId;
        })->count();

        return $unreadCount;
    }

    public function render()
    {
        return view('livewire.unread-badge', [
            'count' => $this->getUnreadCount(),
        ]);
    }

}
