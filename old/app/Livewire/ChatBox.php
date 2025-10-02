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
	'save-edit-message' => 'editMessage',
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
 public function refresh()
    {
        $this->loadMessages();
        $this->dispatch('scroll-bottom');
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

    // ✅ Step: Mark messages as read
    $unreadMessages = Message::where('conversation_id', $this->selectedConversation->id)
        ->where('receiver_id', Auth::id())
        ->whereNull('read_at')
        ->get();

    foreach ($unreadMessages as $msg) {
        $msg->read_at = now();
        $msg->save();

        // 🔄 Notify sender of read status
        $msg->sender->notify(new MessageRead(
            $this->selectedConversation->id
        ));
    }

    return $this->loadedMessages;
}

public function editMessage($data)
{
    $messageId = $data['messageId'];
    $newContent = $data['content'];

    $message = Message::findOrFail($messageId);
    if ($message->sender_id !== auth()->id()) return;

    $message->update([
        'message' => $newContent,
        'edited_at' => now()
    ]);

    $this->refresh();
}
public function deleteMessage($messageId)
{
    $message = Message::findOrFail($messageId);
    
    if ($message->sender_id !== Auth::id()) {
        return;
    }

    $message->delete();
    $this->refresh();
}
    public function broadcastedNotificationReceived($event)
    {
        // dd($event); // Now this should work


        if ($event['type'] == MessageSent::class) {

            if ($event['conversation_id'] == $this->selectedConversation->id) {

                $this->dispatch('scroll-bottom');

                $newMessage = Message::find($event['message_id']);


                #push message
                $this->loadedMessages->push($newMessage);


                // #mark as read
                $newMessage->read_at = now();
                $newMessage->save();

                //     #broadcast
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

    public function sendMessage()
    {
        $this->validate(['message' => 'required|string']);
        // dd([
        //     $this->selectedConversation->getReceiver()->id,
        //     Auth::id(),
        // ]);
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


    public function render()
    {
        return view('livewire.chat-box');
    }
}
