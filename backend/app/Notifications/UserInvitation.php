<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserInvitation extends Notification
{
    use Queueable;

    public function __construct(
        public string $url,
    ) {
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $hours = (int) round(config('auth.passwords.invites.expire') / 60);

        return (new MailMessage)
            ->subject('You have been invited to '.config('app.name'))
            ->greeting("Hello {$notifiable->name},")
            ->line('You have been invited to the '.config('app.name').' admin panel.')
            ->line('Choose a password to activate your account. You will be logged in right after.')
            ->action('Set your password', $this->url)
            ->line("This link expires in {$hours} hours and can only be used once.")
            ->line('If you did not expect this invitation, you can ignore this email.');
    }
}
