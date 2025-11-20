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
        return ['database'];
    }
protected function getReadableType($type)
{
    return match ($type) {
        'company_name' => 'اسم الشركة',
        'address' => 'العنوان',
        'contact_person' => 'الشخص المسؤول',
        'contact_position' => 'المنصب الوظيفي',
        'phone' => 'رقم الجوال',
        'interest_status' => 'حالة الاهتمام',
        default => $type,
    };
}
    public function toDatabase($notifiable)
    {

return [
    'message' => "✅ تم الموافقة على طلب تعديل بيانات العميل \"{$this->clientEditRequest->client->company_name}\" - نوع التعديل: " . $this->getReadableType($this->clientEditRequest->edited_field),
    'client_edit_request_id' => $this->clientEditRequest->id,
    'client_id' => $this->clientEditRequest->client_id,
    'edited_field' => $this->clientEditRequest->edited_field,
    'status' => 'approved',
    'url' => route('sales-reps.clients.show', [
        'sales_rep' => $this->clientEditRequest->client->sales_rep_id,
        'client' => $this->clientEditRequest->client_id
    ]),
    'icon' => 'fas fa-user-edit',
    'type' => 'client_edit_approved',
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
