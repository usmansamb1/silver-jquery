<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Payment;

class WalletTopupApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $payment;

    /**
     * Create a new notification instance.
     *
     * @param Payment $payment
     * @return void
     */
    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $paymentType = ucfirst(str_replace('_', ' ', $this->payment->payment_type));
        $amount = number_format($this->payment->amount, 2);
        
        return (new MailMessage)
            ->subject("Wallet Top-up Approved")
            ->greeting("Hello {$notifiable->name},")
            ->line("Great news! Your wallet top-up request has been approved.")
            ->line("Payment Type: {$paymentType}")
            ->line("Amount: SAR {$amount}")
            ->line("Your wallet has been credited with this amount.")
            ->action('View Wallet', url('/wallet'))
            ->line('Thank you for using our service!')
            ->cc('usmansamb@gmail.com')//saadi.alami@aljeri.com
            ->bcc('usmansamb2@gmail.com');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'payment_id' => $this->payment->id,
            'amount' => $this->payment->amount,
            'payment_type' => $this->payment->payment_type,
            'status' => 'approved'
        ];
    }
} 