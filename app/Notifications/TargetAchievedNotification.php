<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;

class TargetAchievedNotification extends Notification implements ShouldQueue, ShouldBroadcastNow
{
    use Queueable;

    protected $target;
    protected $commission;

    public function __construct($target, $commission = null)
    {
        $this->target = $target;
        $this->commission = $commission;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => '🎯 Target Achieved!',
            'message' => 'You have achieved your monthly target for the service: ' . $this->target->service->name,
            'service_name' => $this->target->service->name,
            'month' => $this->target->month,
            'year' => $this->target->year,
            'achieved_amount' => $this->target->achieved_amount,
            'target_amount' => $this->target->target_amount,
            'url' => route('salesrep.agreements.show', [
                'salesrep' => $this->target->sales_rep_id,
                'agreement' => $this->target->agreement_id,
            ], true),
            'percentage' => number_format(($this->target->achieved_amount / $this->target->target_amount) * 100, 2),
            'commission_amount' => $this->commission?->commission_amount ?? 0,
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'title' => '🎯 Target Achieved!',
            'message' => 'You have achieved your monthly target for the service: ' . $this->target->service->name,
            'service_name' => $this->target->service->name,
            'month' => $this->target->month,
            'year' => $this->target->year,
            'achieved_amount' => $this->target->achieved_amount,
            'target_amount' => $this->target->target_amount,
            'percentage' => number_format(($this->target->achieved_amount / $this->target->target_amount) * 100, 2),
            'commission_amount' => $this->commission?->commission_amount ?? 0,
        ]);
    }

    public function toArray($notifiable)
    {
        return $this->toDatabase($notifiable);
    }
    public function broadcastOn()
    {
        $adminUser = User::where('role', 'admin')->first();

        return [new PrivateChannel('target.achieved.' . $adminUser->id)];
    }
    public function broadcastAs()
    {
        return 'target.achieved';
    }
}
