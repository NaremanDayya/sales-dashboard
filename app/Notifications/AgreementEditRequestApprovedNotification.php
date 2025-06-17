<?php

namespace App\Notifications;

use App\Events\NotificationSent;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class AgreementEditRequestApprovedNotification extends Notification implements ShouldBroadcastNow
{
    use Queueable;

    protected $agreementEditRequest;

    public function __construct($agreementEditRequest)
    {
        $this->agreementEditRequest = $agreementEditRequest;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => "طلب تعديل الاتفاقية رقم #{$this->agreementEditRequest->id} تم قبوله.",
            'agreement_edit_request_id' => $this->agreementEditRequest->id,
            'agreement_id' => $this->agreementEditRequest->agreement_id,
            'edited_field' => $this->agreementEditRequest->edited_field,
            'status' => 'approved',
            'url' => route('admin.agreement-request.update', ['agreement' => $this->agreementEditRequest->agreement_id, 'agreement_request' => $this->agreementEditRequest->id]),
        ];
        event(new NotificationSent($notifiable));

    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'message' => "طلب تعديل الاتفاقية رقم #{$this->agreementEditRequest->id} تم قبوله.",
            'agreement_edit_request_id' => $this->agreementEditRequest->id,
            'agreement_id' => $this->agreementEditRequest->agreement_id,
            'edited_field' => $this->agreementEditRequest->edited_field,
            'status' => 'approved',
            'url' => route('admin.agreement-request.review', ['agreement' => $this->agreementEditRequest->agreement_id, 'agreement_request' => $this->agreementEditRequest->id]),
        ], true);
    }

    public function broadcastOn()
    {
        return [new PrivateChannel('agreement.request.approved.' . $this->agreementEditRequest->salesRep->user->id)];
    }

    public function broadcastAs()
    {
        return 'agreement.edit.request.approved';
    }
}
