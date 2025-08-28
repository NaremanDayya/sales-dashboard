<?php

namespace App\Livewire;

use App\Models\Message;
use App\Notifications\MessageRead;
use App\Notifications\MessageSent;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ChatBox extends Component
{
    public $selectedConversation;
    public $message;
    public $loadedMessages;
    public $paginate_var = 10;
    public $client_id;
    public $last_contact_date;

    protected $listeners = [
        'loadMore',
        'broadcastedNotificationReceived',
    ];

    public function getListeners()
    {
        $auth_id = Auth::id();
        return [
            'loadMore',
            'refresh' => 'refresh',
            "echo-private:chat.{$auth_id},Illuminate\\Notifications\\Events\\BroadcastNotificationCreated" => 'broadcastedNotificationReceived',
        ];
    }

    public function mount($client_id = null)
    {
        $this->client_id = $client_id;

        if ($this->selectedConversation) {
            $this->loadMessages();
            $this->last_contact_date = $this->selectedConversation->client->last_contact_date ?? null;
        }
    }

    public function loadMore(): void
    {
        $this->paginate_var += 10;
        $this->loadMessages();
        $this->dispatch('update-chat-height');
    }

    public function loadMessages()
    {
        $count = Message::where('conversation_id', $this->selectedConversation->id)->count();

        $this->loadedMessages = Message::where('conversation_id', $this->selectedConversation->id)
            ->skip($count - $this->paginate_var)
            ->take($this->paginate_var)
            ->get();

        return $this->loadedMessages;
    }

    public function broadcastedNotificationReceived($event)
    {
        if ($event['type'] == MessageSent::class) {
            if ($event['conversation_id'] == $this->selectedConversation->id) {
                $this->dispatch('scroll-bottom');

                $newMessage = Message::find($event['message_id']);

                #push message
                $this->loadedMessages->push($newMessage);

                // #mark as read
                $newMessage->read_at = now();
                $newMessage->save();

                //#broadcast
                $this->selectedConversation->getReceiver()
                    ->notify(new MessageRead(
                        Auth::user(),
                        $newMessage,
                        $this->selectedConversation,
                        $newMessage->receiver_id,
                    ));
            }
        }
    }

    public function sendLike()
    {
        $createdMessage = Message::create([
            'conversation_id' => $this->selectedConversation->id,
            'sender_id' => Auth::id(),
            'receiver_id' => $this->selectedConversation->getReceiver()->id,
            'message' => 'like'
        ]);
        $this->loadedMessages->push($createdMessage);

        $this->selectedConversation->updated_at = now();
        $this->selectedConversation->save();

        $this->dispatch('refresh')->to('chat-list');

        $receiver = $this->selectedConversation->getReceiver();
        $receiver->notify(new MessageSent(
            Auth::user(),
            $createdMessage,
            $this->selectedConversation,
            $receiver->id,
        ));

        $this->dispatch('scroll-bottom');
    }

    public function sendMessage()
    {
        $this->validate(['message' => 'required|string']);

        $createdMessage = Message::create([
            'conversation_id' => $this->selectedConversation->id,
            'sender_id' => Auth::id(),
            'receiver_id' => $this->selectedConversation->getReceiver()->id,
            'message' => $this->message
        ]);

        $this->reset('message');

        #scroll to bottom
        $this->dispatch('scroll-bottom');

        #push the message
        $this->loadedMessages->push($createdMessage);

        #update conversation model
        $this->selectedConversation->updated_at = now();
        $this->selectedConversation->save();

        #refresh chatlist
        $this->dispatch('refresh')->to('chat-list');

        #broadcast
        $receiver = $this->selectedConversation->getReceiver();
        $messageReceiver = $createdMessage->receiver->id;
        $receiver->notify(new MessageSent(
            Auth::user(),
            $createdMessage,
            $this->selectedConversation,
            $messageReceiver,
        ));
    }

    public function editMessage($messageId, $newMessage)
    {
        $message = Message::find($messageId);

        // Check if the user owns this message
        if ($message && $message->sender_id === Auth::id()) {
            $message->message = $newMessage;
            $message->edited_at = now();
            $message->save();

            // Refresh messages
            $this->loadMessages();

            // Notify the receiver about the edit
            $receiver = $this->selectedConversation->getReceiver();
            $receiver->notify(new MessageSent(
                Auth::user(),
                $message,
                $this->selectedConversation,
                $receiver->id,
            ));
        }
    }

    public function deleteMessage($messageId)
    {
        $message = Message::find($messageId);

        // Check if the user owns this message
        if ($message && $message->sender_id === Auth::id()) {
            $message->delete();

            // Refresh messages
            $this->loadMessages();

            // Update conversation timestamp
            $this->selectedConversation->updated_at = now();
            $this->selectedConversation->save();

            // Refresh chat list
            $this->dispatch('refresh')->to('chat-list');
        }
    }

    public function refresh()
    {
        $this->loadMessages();
        $this->dispatch('scroll-bottom');
    }

    public function render()
    {
        return view('livewire.chat-box');
    }
}
