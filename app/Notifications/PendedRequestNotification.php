<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PendedRequestNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }
    public function toDatabase($notifiable)
    {
        return [
            'message' => 'يوجد طلبات تعديل معلقة منذ أكثر من 3 أيام. الرجاء مراجعتها.',
            'type' => 'pended_requests',
            'url' => route('admin.allRequests'), // adjust the route
        ];
    }
    public function toBroadcast($notifiable)
    {
        return [
            'message' => 'يوجد طلبات تعديل معلقة منذ أكثر من 3 أيام. الرجاء مراجعتها.',
            'type' => 'pended_requests',
            'url' => route('admin.allPendingRequests'), // adjust the route
        ];
    }
    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function broadcastOn()
    {
        $adminUser = User::where('role', 'admin')->first(); // get the first admin

        return [
            new PrivateChannel('pended-request.notice.' . $adminUser->id),
        ];
    }

}
