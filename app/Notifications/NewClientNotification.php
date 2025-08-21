<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewClientNotification extends Notification implements ShouldQueue, ShouldBroadcastNow
{
    use Queueable;

    public $client;

    /**
     * Create a new notification instance.
     */
    public function __construct($client)
    {
        $this->client = $client;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast']; // Add 'mail' if you want email notifications
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Client Created')
            ->line('A new client has been added to the system.')
            ->action('View Client', route('sales-reps.clients.show', [
                'sales_rep' => $this->client->sales_rep_id,
                'client' => $this->client->id,
            ]))->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification for database storage.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'client_id' => $this->client->id,
            'client_name' => $this->client->company_name,
            'message' => 'A new client (' . $this->client->name . ') has been created.',
            'url' => route('sales-reps.clients.show', [
                'sales_rep' => $this->client->sales_rep_id,
                'client' => $this->client->id,
            ], true),
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'client_id' => $this->client->id,
            'client_name' => $this->client->name,
            'message' => 'A new client (' . $this->client->company_name . ') has been created.',
            'url' => route('sales-reps.clients.show', [
                'sales_rep' => $this->client->sales_rep_id,
                'client' => $this->client->id,
            ]),
            'created_at' => now()->toDateTimeString(),
        ]);
    }
    public function toDatabase(object $notifiable): array
    {
        return [
            'client_id' => $this->client->id,
            'client_name' => $this->client->company_name,
	'message' => "🆕 تم إضافة عميل جديد: \"{$this->client->company_name}\" إلى النظام.",
            'url' => route('sales-reps.clients.show', [
                'sales_rep' => $this->client->sales_rep_id,
                'client' => $this->client->id,
            ]),
        ];
    }
    public function broadcastOn(): array
    {
        $adminUser = User::where('role', 'admin')->first();

        return [new PrivateChannel('new-client.' . $adminUser->id)];
    }

    public function broadcastAs()
    {
        return 'Client Created';
    }
}
