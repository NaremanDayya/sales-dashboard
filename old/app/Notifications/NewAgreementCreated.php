<?php

namespace App\Notifications;

use App\Models\Agreement;
use App\Models\User;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Support\Facades\Log;

class NewAgreementCreated extends Notification implements ShouldQueue, ShouldBroadcastNow
{
    use Queueable;

    public Agreement $agreement;

    public function __construct(Agreement $agreement)
    {
        $this->agreement = $agreement;
    }

    public function via(object $notifiable): array
    {
        Log::info('Saving agreement notification to DB', ['agreement_id' => $this->agreement->id]);
        return ['database', 'broadcast'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'تم إنشاء اتفاقية جديدة',
            'message' => 'تمت إضافة اتفاقية جديدة مع العميل ' . $this->agreement->client->company_name,
            'agreement_id' => $this->agreement->id,
            'sales_rep_id' => $this->agreement->sales_rep_id,
            'url' => route('salesrep.agreements.show', [
                'salesrep' => $this->agreement->sales_rep_id,
                'agreement' => $this->agreement->id,
            ], true)
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'title' => 'تم إنشاء اتفاقية جديدة',
            'message' => 'تمت إضافة اتفاقية جديدة مع العميل ' . $this->agreement->client->company_name,
            'agreement_id' => $this->agreement->id,
            'sales_rep' => $this->agreement->salesRep->name,
            'client' => $this->agreement->client->company_name,
            'signing_date' => $this->agreement->signing_date->format('Y-m-d'),
            'total_amount' => $this->agreement->total_amount,
            'url' => route('salesrep.agreements.show', [
                'salesrep' => $this->agreement->sales_rep_id,
                'agreement' => $this->agreement->id,
            ]),
        ]);
    }

    public function broadcastOn(): array
    {
        $adminUser = User::where('role', 'admin')->first();

        return [new PrivateChannel('new-agreement.' . $adminUser->id)];
    }

}
