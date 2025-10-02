<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class NewAgreementEditRequestNotification extends Notification implements ShouldBroadcastNow
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
            'message' => "تم إرسال طلب تعديل جديد للاتفاقية الخاصة بالعميل  {$this->agreementEditRequest->client->company_name}.",
            'agreement_edit_request_id' => $this->agreementEditRequest->id,
            'agreement_id' => $this->agreementEditRequest->agreement_id,
            'client_id' => $this->agreementEditRequest->client_id,
            'client_name' => optional($this->agreementEditRequest->client)->company_name,
            'edited_field' => $this->agreementEditRequest->edited_field,
            'status' => $this->agreementEditRequest->status,
            'url' => route('admin.allRequests'),
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'message' => "تم إرسال طلب تعديل جديد للاتفاقية رقم #{$this->agreementEditRequest->id}.",
            'agreement_edit_request_id' => $this->agreementEditRequest->id,
            'agreement_id' => $this->agreementEditRequest->agreement_id,
            'client_id' => $this->agreementEditRequest->client_id,
            'client_name' => optional($this->agreementEditRequest->client)->company_name,
            'edited_field' => $this->agreementEditRequest->edited_field,
            'status' => $this->agreementEditRequest->status,
            'url' => route('admin.agreement-request.review', ['agreement' => $this->agreementEditRequest->agreement_id, 'agreement_request' => $this->agreementEditRequest->id]),
        ], true);
    }

    public function broadcastOn()
    {
        $adminUser = User::where('role', 'admin')->first();

        return [new PrivateChannel('agreement.request.sent.' . $adminUser->id)];
    }

    public function broadcastAs()
    {
        return 'agreement.edit.request.new';
    }
}
