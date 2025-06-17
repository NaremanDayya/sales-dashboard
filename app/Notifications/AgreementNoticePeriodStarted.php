<?php

namespace App\Notifications;

use App\Models\Agreement;
use App\Models\User;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class AgreementNoticePeriodStarted extends Notification implements ShouldBroadcast
{
    use Queueable;

    protected $agreement;

    public function __construct(Agreement $agreement)
    {
        $this->agreement = $agreement;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'إشعار بدء فترة الإخطار للاتفاقية',
            'message' => "الاتفاقية الخاصة بالعميل: {$this->agreement->client->company_name} قد دخلت فترة الإشعار.",
            'agreement_id' => $this->agreement->id,
            'client_id' => $this->agreement->client_id,
            'end_date' => $this->agreement->end_date->format('Y-m-d'),
            'notice_months' => $this->agreement->notice_months,
            'type' => 'agreement_notice',
            'url' => route('salesrep.agreements.show', [
                'salesrep' => $this->agreement->sales_rep_id,
                'agreement' => $this->agreement->id,
            ], true),
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'title' => 'إشعار بدء فترة الإخطار للاتفاقية',
            'message' => "الاتفاقية الخاصة بالعميل: {$this->agreement->client->company_name} قد دخلت فترة الإشعار.",
            'agreement_id' => $this->agreement->id,
            'client_id' => $this->agreement->client_id,
            'end_date' => $this->agreement->end_date->format('Y-m-d'),
            'notice_months' => $this->agreement->notice_months,
            'type' => 'agreement_notice',
            'url' => route('salesrep.agreements.show', [
                'salesrep' => $this->agreement->sales_rep_id,
                'agreement' => $this->agreement->id,
            ], true),
        ]);
    }

    public function broadcastOn()
    {
        $adminUser = User::where('role', 'admin')->first(); // get the first admin

        return [
            new PrivateChannel('agreement.notice.' . $this->agreement->salesRep->user->id),
            new PrivateChannel('agreement.notice.' . $adminUser->id),
        ];
    }
    public function broadcastAs()
    {
        return 'agreement.notice.started';
    }
}
