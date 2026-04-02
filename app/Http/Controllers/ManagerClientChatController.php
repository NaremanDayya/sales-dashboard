<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ManagerClientChat;
use App\Models\ManagerChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ManagerClientChatController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $chats = ManagerClientChat::where('sales_rep_id', $user->id)
            ->orWhere('manager_id', $user->id)
            ->with(['client', 'salesRep', 'manager', 'latestMessage'])
            ->get()
            ->map(function ($chat) use ($user) {
                $chat->unread_count = $chat->unreadMessagesFor($user->id);
                return $chat;
            });

        return view('manager.chats.index', compact('chats'));
    }

    public function show(ManagerClientChat $chat)
    {
        $user = Auth::user();

        if ($chat->sales_rep_id !== $user->id && $chat->manager_id !== $user->id) {
            abort(403, 'You do not have access to this chat.');
        }

        $messages = $chat->messages()
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();

        $chat->messages()
            ->where('sender_id', '!=', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return view('manager.chats.show', compact('chat', 'messages'));
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

        $chat = ManagerClientChat::firstOrCreate([
            'client_id' => $client->id,
            'sales_rep_id' => $user->id,
            'manager_id' => $salesRep->manager->user_id,
        ]);

        return redirect()->route('manager.chats.show', $chat);
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

        $chat = ManagerClientChat::firstOrCreate([
            'client_id' => $client->id,
            'sales_rep_id' => $client->salesRep->user_id,
            'manager_id' => $user->id,
        ]);

        return redirect()->route('manager.chats.show', $chat);
    }

    public function sendMessage(Request $request, ManagerClientChat $chat)
    {
        $user = Auth::user();

        if ($chat->sales_rep_id !== $user->id && $chat->manager_id !== $user->id) {
            abort(403, 'You do not have access to this chat.');
        }

        $request->validate([
            'message' => 'required|string|max:5000',
        ]);

        DB::transaction(function () use ($chat, $request, $user) {
            ManagerChatMessage::create([
                'manager_client_chat_id' => $chat->id,
                'sender_id' => $user->id,
                'message' => $request->message,
            ]);
        });

        return redirect()->back()->with('success', 'Message sent successfully.');
    }
}
