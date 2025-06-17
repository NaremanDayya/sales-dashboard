<?php

namespace App\Notifications;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class ClientEditRequestRejectedNotification extends Notification implements ShouldBroadcastNow
{
    use Queueable;

    protected $clientEditRequest;

    public function __construct($clientEditRequest)
    {
        $this->clientEditRequest = $clientEditRequest;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => "طلب تعديل العميل رقم #{$this->clientEditRequest->id} تم رفضه.",
            'client_edit_request_id' => $this->clientEditRequest->id,
            'client_id' => $this->clientEditRequest->client_id,
            'edited_field' => $this->clientEditRequest->request_type,
            'status' => 'rejected',
            'url' => route(
                'sales-reps.client-requests.show',
                [
                    'client' => $this->clientEditRequest->client_id,
                    'client_request' => $this->clientEditRequest->id,
                ],
                true
            ),
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'message' => "طلب تعديل العميل رقم #{$this->clientEditRequest->id} تم رفضه.",
            'client_edit_request_id' => $this->clientEditRequest->id,
            'client_id' => $this->clientEditRequest->client_id,
            'edited_field' => $this->clientEditRequest->edited_field,
            'status' => 'rejected',
            'url' => route(
                'sales-reps.client-requests.show',
                [
                    'client' => $this->clientEditRequest->client_id,
                    'client_request' => $this->clientEditRequest->id,
                ]
            ),
        ]);
    }

    public function broadcastOn()
    {
        return [new PrivateChannel('client.request.rejected.' . $this->clientEditRequest->salesRep->user->id)];
    }

    public function broadcastAs()
    {
        return 'client.edit.request.rejected';
    }
}
