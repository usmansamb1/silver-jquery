<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Payment;

class WalletTopupRejectedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $payment;
    protected $reason;

    /**
     * Create a new notification instance.
     *
     * @param Payment $payment
     * @param string|null $reason
     * @return void
     */
    public function __construct(Payment $payment, $reason = null)
    {
        $this->payment = $payment;
        $this->reason = $reason;
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
        
        $mail = (new MailMessage)
            ->subject("Wallet Top-up Request Rejected")
            ->greeting("Hello {$notifiable->name},")
            ->line("We regret to inform you that your wallet top-up request has been rejected.")
            ->line("Payment Type: {$paymentType}")
            ->line("Amount: SAR {$amount}")
            ->cc('usmansamb@gmail.com')//saadi.alami@aljeri.com
            ->bcc('usmansamb2@gmail.com');
            
        if ($this->reason) {
            $mail->line("Reason for rejection: {$this->reason}");
        }
        
        return $mail
            ->line("Please contact our support team if you have any questions.")
            ->action('Submit New Request', url('/wallet/topup'))
            ->line('Thank you for your understanding.');
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
            'status' => 'rejected',
            'reason' => $this->reason
        ];
    }
} 