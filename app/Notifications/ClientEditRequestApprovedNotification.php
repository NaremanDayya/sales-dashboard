<?php

namespace App\Notifications;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class ClientEditRequestApprovedNotification extends Notification implements ShouldBroadcastNow
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
            'message' => "طلب تعديل العميل رقم #{$this->clientEditRequest->id} تم الموافقة عليه.",
            'client_edit_request_id' => $this->clientEditRequest->id,
            'client_id' => $this->clientEditRequest->client_id,
            'edited_field' => $this->clientEditRequest->request_type,
            'status' => 'approved',
            'url' => route('admin.client-request.update', ['client' => $this->clientEditRequest->client_id, 'client_request' => $this->clientEditRequest->id]),
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'message' => "طلب تعديل العميل رقم #{$this->clientEditRequest->id} تم الموافقة عليه.",
            'client_edit_request_id' => $this->clientEditRequest->id,
            'client_id' => $this->clientEditRequest->client_id,
            'edited_field' => $this->clientEditRequest->edited_field,
            'status' => 'approved',
            'url' => route('sales-reps.client-requests.show',
             ['client' => $this->clientEditRequest->client_id,
              'client_request' => $this->clientEditRequest->id]),
        ], true);
    }

    public function broadcastOn()
    {
    return [new PrivateChannel('client.request.approved.' .$this->clientEditRequest->salesRep->user->id)];

    }

    public function broadcastAs()
    {
        return 'client.edit.request.approved';
    }
}
