<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ManagerClientChatController extends Controller
{
    public function index()
    {
        // Redirect to chat container with manager chats
        return view('livewire.chat-container', [
            'initialConversationId' => null,
            'client' => null,
        ]);
    }

    public function show(Client $client)
    {
        $user = Auth::user();
        $salesRep = $user->salesRep;

        // Verify access: user must be manager of the client's sales rep
        if (!$salesRep || !$salesRep->isManager() || $client->salesRep->manager_id !== $salesRep->id) {
            abort(403, 'You do not have access to this chat.');
        }

        // Find or create conversation between manager and team member about this client
        $conversation = Conversation::firstOrCreate(
            [
                'sender_id' => $user->id,
                'receiver_id' => $client->salesRep->user_id,
                'client_id' => $client->id,
                'is_manager_chat' => true,
            ]
        );

        // Redirect to chat container with this conversation
        return redirect()->route('client.chat', [
            'client' => $client->id,
            'conversation' => $conversation->id,
        ]);
    }

    public function store(Request $request, Client $client)
    {
        $user = Auth::user();
        $salesRep = $user->salesRep;

        if (!$salesRep || !$salesRep->hasManager()) {
            abort(403, 'You do not have a manager assigned.');
        }

        if ($client->sales_rep_id !== $salesRep->id) {
            abort(403, 'This client is not assigned to you.');
        }

        // Find or create conversation between sales rep and their manager about this client
        $conversation = Conversation::firstOrCreate(
            [
                'sender_id' => $user->id,
                'receiver_id' => $salesRep->manager->user_id,
                'client_id' => $client->id,
                'is_manager_chat' => true,
            ]
        );

        return redirect()->route('client.chat', [
            'client' => $client->id,
            'conversation' => $conversation->id,
        ]);
    }

    public function createFromManager(Request $request, Client $client)
    {
        $user = Auth::user();
        $salesRep = $user->salesRep;

        if (!$salesRep || !$salesRep->isManager()) {
            abort(403, 'You are not a manager.');
        }

        if ($client->salesRep->manager_id !== $salesRep->id) {
            abort(403, 'This client does not belong to your team.');
        }

        // Find or create conversation between manager and team member about this client
        $conversation = Conversation::firstOrCreate(
            [
                'sender_id' => $user->id,
                'receiver_id' => $client->salesRep->user_id,
                'client_id' => $client->id,
                'is_manager_chat' => true,
            ]
        );

        return redirect()->route('client.chat', [
            'client' => $client->id,
            'conversation' => $conversation->id,
        ]);
    }
}
