<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class ClientEditRequestNotification extends Notification implements ShouldBroadcastNow
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
            'message' => "تم إرسال طلب تعديل جديد لعميل رقم #{$this->clientEditRequest->client_id}.",
            'client_edit_request_id' => $this->clientEditRequest->id,
            'client_id' => $this->clientEditRequest->client_id,
            'edited_field' => $this->clientEditRequest->request_type,
            'status' => $this->clientEditRequest->status,
            'url' => route('admin.client-request.edit',
             ['client' => $this->clientEditRequest->client_id,
             'client_request' => $this->clientEditRequest->id
            ], true),
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'message' => "تم إرسال طلب تعديل جديد لعميل رقم #{$this->clientEditRequest->client_id}.",
            'client_edit_request_id' => $this->clientEditRequest->id,
            'client_id' => $this->clientEditRequest->client_id,
            'edited_field' => $this->clientEditRequest->request_type,
            'status' => $this->clientEditRequest->status,
            'url' => route('admin.client-request.update', ['client' => $this->clientEditRequest->client_id, 'client_request' => $this->clientEditRequest->id]),
        ]);
    }

    public function broadcastOn()
    {
        $adminUser = User::where('role', 'admin')->first();

        return [new PrivateChannel('client.request.sent.' . $adminUser->id)];
   }
   public function broadcastAs()
    {
        return 'new.client.edit.request';
    }
}
