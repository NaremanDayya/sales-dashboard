<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use App\Models\Agreement;
use App\Models\User;
use Illuminate\Broadcasting\PrivateChannel;

class AgreementRenewed extends Notification implements ShouldQueue
{
    use Queueable;

    public $agreement;

    public function __construct(Agreement $agreement)
    {
        $this->agreement = $agreement;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('🔄 تم تجديد الاتفاقية')
            ->greeting('مرحباً!')
            ->line('تم تجديد الاتفاقية الخاصة بالعميل: **' . $this->agreement->client->company_name . '**')
            ->line('📅 تاريخ التجديد: ' . $this->agreement->signing_date->format('Y-m-d'))
            ->line('🆕 تنتهي الاتفاقية الجديدة في: ' . $this->agreement->end_date->format('Y-m-d'))
            ->action('عرض الاتفاقية', route('salesrep.agreements.show', [
                'salesrep' => $this->agreement->sales_rep_id,
                'agreement' => $this->agreement->id,
            ]))
            ->line('شكراً لاستخدامكم نظام إدارة المبيعات.');
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => '🔄 تم تجديد الاتفاقية الخاصة بالعميل "' . $this->agreement->client->company_name . '" حتى تاريخ ' . $this->agreement->end_date->format('Y-m-d') . '.',
            'agreement_id' => $this->agreement->id,
            'url' => route('salesrep.agreements.show', [
                'salesrep' => $this->agreement->sales_rep_id,
                'agreement' => $this->agreement->id,
            ]),
            'icon' => 'fas fa-file-contract',
            'type' => 'agreement_renewed',
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'message' => '🔄 تم تجديد الاتفاقية الخاصة بالعميل "' . $this->agreement->client->company_name . '" حتى تاريخ ' . $this->agreement->end_date->format('Y-m-d') . '.',
            'agreement_id' => $this->agreement->id,
            'url' => route('salesrep.agreements.show', [
                'salesrep' => $this->agreement->sales_rep_id,
                'agreement' => $this->agreement->id,
            ]),
            'icon' => 'fas fa-file-contract',
            'type' => 'agreement_renewed',
            'created_at' => now()->toDateTimeString(),
            'read_at' => null,
        ]);
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('agreement.renewed.admin'),
            new PrivateChannel('agreement.renewed.' . $this->agreement->sales_rep_id),
        ];
    }
}
