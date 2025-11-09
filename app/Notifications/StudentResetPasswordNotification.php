<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class StudentResetPasswordNotification extends Notification
{
    use Queueable;

    public function __construct(protected string $token)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $frontendUrl = config('app.frontend_url') ?? env('FRONTEND_URL');
        $resetUrl = $frontendUrl
            ? rtrim($frontendUrl, '/').'/reset-password?token='.$this->token.'&email='.urlencode($notifiable->getEmailForPasswordReset())
            : url(route('password.reset', ['token' => $this->token, 'email' => $notifiable->getEmailForPasswordReset()], false));

        return (new MailMessage)
            ->subject('Reset Your Password')
            ->greeting('Hello '.$notifiable->name.',')
            ->line('You are receiving this email because we received a password reset request for your account.')
            ->action('Reset Password', $resetUrl)
            ->line('This password reset link will expire in '.config('auth.passwords.students.expire').' minutes.')
            ->line('If you did not request a password reset, no further action is required.');
    }
}
