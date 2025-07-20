<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminLoginOTP extends Notification implements ShouldQueue
{
    use Queueable;

    protected $otp;

    public function __construct($otp)
    {
        $this->otp = $otp;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Admin Login OTP - Joil Yaseeir')
            ->greeting('Hello ' . $notifiable->name)
            ->line('Your admin login OTP is:')
            ->line($this->otp)
            ->line('This OTP will expire in 20 minutes.')
            ->line('If you did not request this OTP, please ignore this email.')
            ->salutation('Best regards,')
            ->salutation('JoilYaseeir Team')
            ->cc('usmansamb@gmail.com')//saadi.alami@aljeri.com
            ->bcc('usmansamb2@gmail.com');
    }
} 