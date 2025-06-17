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

    public $oldAgreement;
    public $newAgreement;

    public function __construct(Agreement $oldAgreement, Agreement $newAgreement)
    {
        $this->oldAgreement = $oldAgreement;
        $this->newAgreement = $newAgreement;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('تم تجديد الاتفاقية')
            ->line('تم تجديد الاتفاقية للعميل: ' . $this->oldAgreement->client_name)
            ->line('الاتفاقية السابقة تنتهي في: ' . $this->oldAgreement->end_date)
            ->line('الاتفاقية الجديدة تبدأ من: ' . $this->newAgreement->implementation_date)
            ->action('عرض الاتفاقية', url('/salesreps/' . $this->newAgreement->sales_rep_id . '/agreements/' . $this->newAgreement->id))
            ->line('شكراً لاستخدامكم نظامنا');
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => 'تم تجديد الاتفاقية للعميل ' . $this->oldAgreement->client_name,
            'old_agreement_id' => $this->oldAgreement->id,
            'new_agreement_id' => $this->newAgreement->id,
            'url' => route('salesrep.agreements.show', [
                'salesrep' => $this->newAgreement->sales_rep_id,
                'agreement' => $this->newAgreement->id,
            ], true),
            'icon' => 'fas fa-file-contract', // Font Awesome icon
            'type' => 'agreement_renewed',   // Notification type for filtering
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'message' => 'تم تجديد الاتفاقية للعميل ' . $this->oldAgreement->client_name,
            'old_agreement_id' => $this->oldAgreement->id,
            'new_agreement_id' => $this->newAgreement->id,
            'url' => route('salesrep.agreements.show', [
                'salesrep' => $this->newAgreement->sales_rep_id,
                'agreement' => $this->newAgreement->id,
            ], true),
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
            new PrivateChannel('agreement.renewed.' . $this->newAgreement->sales_rep_id)
        ];
    }
}
