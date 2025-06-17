<?php

namespace App\Notifications;

use App\Models\Client;
use App\Models\User;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class LateCustomerNotification extends Notification implements ShouldBroadcast
{
    use Queueable;

    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'تنبيه العملاء المتأخرين',
            'message' => "يجب عليك التواصل مع العميل: {$this->client->company_name}، لم يتم الاتصال به منذ أكثر من 3 أيام.",
            'client_id' => $this->client->id,
            'last_contact_date' => $this->client->last_contact_date->format('Y-m-d'),
            'type' => 'late_customer',
            'url' => route('sales-reps.clients.show', [
                'sales_rep' => $this->client->sales_rep_id,
                'client' => $this->client->id,
            ], true),
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'title' => 'تنبيه العملاء المتأخرين',
            'message' => "يجب عليك التواصل مع العميل: {$this->client->company_name}، لم يتم الاتصال به منذ أكثر من 3 أيام.",
            'client_id' => $this->client->id,
            'last_contact_date' => $this->client->last_contact_date?->format('Y-m-d'),
            'type' => 'late_customer',
            'url' => route('sales-reps.clients.show', [
                'sales_rep' => $this->client->sales_rep_id,
                'client' => $this->client->id,
            ], true),
        ]);
    }

    public function broadcastOn()
{
    $adminUser = User::where('role', 'admin')->first(); // get the first admin

    return [
        new PrivateChannel('late.customer.' . $this->client->salesRep->user->id),
        new PrivateChannel('agreement.notice.' . $adminUser->id),
    ];
}
    public function broadcastAs()
    {
        return 'late.customer.notification';
    }
}
