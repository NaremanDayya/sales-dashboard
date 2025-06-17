<?php

namespace App\Events;

use App\Models\Message;
use Carbon\Carbon;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class MessageCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithQueue,InteractsWithSockets, Queueable ,SerializesModels;

    public $message;
    // public $receiver;

    public function __construct(Message $message)
    {
        $this->message = $message->load([]);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\PrivateChannel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('conversation.'.$this->message->conversation_id),
        ];
    }

    public function broadcastWhen(): bool
    {
        // Check if the message is not older than 1 minutes
        $isNotExpired = Carbon::parse($this->message->created_at)->gt(Carbon::now()->subMinute());

        return $isNotExpired;
    }

    /**
     * The name of the queue on which to place the broadcasting job.
     */

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'message' => [
                'id' => $this->message->id,
                'conversation_id' => $this->message->conversation_id,
            ],

        ];
    }
}

