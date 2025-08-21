<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BlockedIpLoginAttempt extends Notification
{
    use Queueable;

    public $salesRep;
    public $ip;

    public function __construct($salesRep, $ip)
    {
        $this->salesRep = $salesRep;
        $this->ip = $ip;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'محاولة دخول من جهاز غير مصرح',
            'message' => "قام سفير العلامة التجارية {$this->salesRep->name} بمحاولة تسجيل دخول من IP غير مصرح: {$this->ip}",
            'sales_rep_id' => $this->salesRep->id,
            'ip_address' => $this->ip,
            'time' => now()->format('Y-m-d H:i:s'),
        ];
    }

    public function broadcastOn(): array
    {
        return ['salesrep-login-ip'];
    }
}
