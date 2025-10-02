<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function message(Client $client)
    {
        $authenticatedUserId = Auth::id();
        $authenticatedUser = Auth::user();
        if ($authenticatedUser->role == 'admin') {
            $receiverId = $client->salesRep->user->id;
        } else {
            $receiverId = User::where('role', 'admin')->first()->id; // or use a specific admin if there's more than one
        }
        $existingConversation = Conversation::where(function ($query) use ($authenticatedUserId, $receiverId) {
            $query->where(function ($q) use ($authenticatedUserId, $receiverId) {
                $q->where('sender_id', $authenticatedUserId)
                    ->where('receiver_id', $receiverId);
            })->orWhere(function ($q) use ($authenticatedUserId, $receiverId) {
                $q->where('sender_id', $receiverId)
                    ->where('receiver_id', $authenticatedUserId);
            });
        })
            ->where('client_id', $client->id)
            ->first();

        if ($existingConversation) {
            # Conversation already exists, redirect to existing conversation
            return redirect()->route('client.chat', [
                'client' => $client->id,
                'conversation' => $existingConversation->id
            ]);
        }

        $createdConversation = Conversation::create([
            'sender_id' => $authenticatedUserId,
            'receiver_id' => $receiverId,
            'client_id' => $client->id,
        ]);

        return redirect()->route('client.chat', [
            'client' => $client->id,
            'conversation' => $createdConversation->id
        ]);

    }
    public function unreadConversationsCount()
    {
        $userId = Auth::id();
        $unreadConversations = Conversation::with([
            'client',
            'latestMessage' => function ($query) {
                $query->latest('created_at'); // Use created_at for the latest message
            }
        ])
            ->whereHas('messages', function ($query) use ($userId) {
                $query->where('receiver_id', $userId)
                    ->whereNull('read_at');
            });
        $user = Auth::user();

        $conversations = Conversation::where(function ($query) use ($user) {
            $query->where('sender_id', $user->id)
                ->orWhere('receiver_id', $user->id);
        })->latest('updated_at')
            ->get();


        return response()->json([
            'unread_conversations_count' => $unreadConversations->count(),
            'conversations' => $conversations,
        ]);
    }
}

