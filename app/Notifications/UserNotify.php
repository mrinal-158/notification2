<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserNotify extends Notification implements ShouldQueue
{
    use Queueable;

    public $messageMail;
    public $messageDB;

    /**
     * Create a new notification instance.
     */
    public function __construct($messageMail, $messageDB)
    {
        $this->messageMail = $messageMail;
        $this->messageDB = $messageDB;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->messageDB)
            ->line('YOUR OTP: ' . $this->messageMail)
            ->action('View Dashboard', url('/dashboard'))
            ->line('Thank you for using our app!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => $this->messageDB,
        ];
    }
}
