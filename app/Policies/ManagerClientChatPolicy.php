<?php

namespace App\Policies;

use App\Models\ManagerClientChat;
use App\Models\User;

class ManagerClientChatPolicy
{
    public function view(User $user, ManagerClientChat $chat): bool
    {
        return $chat->sales_rep_id === $user->id || $chat->manager_id === $user->id;
    }

    public function create(User $user): bool
    {
        $salesRep = $user->salesRep;
        return $salesRep && $salesRep->hasManager();
    }

    public function sendMessage(User $user, ManagerClientChat $chat): bool
    {
        return $chat->sales_rep_id === $user->id || $chat->manager_id === $user->id;
    }
}
