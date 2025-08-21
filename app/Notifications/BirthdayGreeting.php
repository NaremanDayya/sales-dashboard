<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BirthdayGreeting extends Notification
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
        return ['database'];
    }


    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => '🎉 تهانينا بعيد ميلادك!',
            'message' => "كل عام وأنت بخير يا {$notifiable->name}! 🎂 نتمنى لك عامًا مليئًا بالنجاح والسعادة!",
            'icon' => '🎂',
        ];
    }

	 public function broadcastOn(): array
    {
        return ['birthday'];
    }
}
