<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class GenericClientRequestRejectedNotification extends Notification implements ShouldBroadcastNow
{
    use Queueable;

 protected $clientRequest;

    public function __construct($clientRequest)
    {
        $this->clientRequest = $clientRequest;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => "طلب العميل رقم #{$this->clientRequest->client->company_name} تم رفضه.",
            'client_request_id' => $this->clientRequest->id,
            'client_id' => $this->clientRequest->client_id,
            'status' => 'rejected',
'url' =>route('sales-reps.clientRequests.show', [
                'client' => $this->clientRequest->client_id,
                'client_request' => $this->clientRequest->id
        ]),
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'message' => "طلب العميل رقم #{$this->clientRequest->id} تم رفضه.",
            'client_request_id' => $this->clientRequest->id,
            'client_id' => $this->clientRequest->client_id,
            'status' => 'rejected',
'url' =>route('sales-reps.clientRequests.show', [
                'client' => $this->clientRequest->client_id,
                'client_request' => $this->clientRequest->id
        ]),
        ]);
    }

    public function broadcastOn()
    {
        return [new PrivateChannel('client.request.rejected.' . $this->clientRequest->salesRep->id)];
    }

    public function broadcastAs()
    {
        return 'client.request.rejected';
    }

}
