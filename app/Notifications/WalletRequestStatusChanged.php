<?php

namespace App\Notifications;

use App\Models\WalletApprovalRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WalletRequestStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    protected $walletRequest;
    protected $status;
    protected $message;

    /**
     * Create a new notification instance.
     *
     * @param  WalletApprovalRequest  $walletRequest
     * @param  string  $status
     * @param  string  $message
     * @return void
     */
    public function __construct(WalletApprovalRequest $walletRequest, string $status, string $message = '')
    {
        $this->walletRequest = $walletRequest;
        $this->status = $status;
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $amount = $this->walletRequest->payment ? $this->walletRequest->payment->amount : 0;
        $statusText = ucfirst($this->status);
        $adminName = auth()->user()->name ?? 'Administrator';
        
        $mail = (new MailMessage)
            ->subject("Wallet Top-up Request {$statusText}")
            ->greeting("Hello {$notifiable->name},")
            ->line("Wallet top-up request {$this->walletRequest->id} has been **{$this->status} by the administrator ({$adminName}).**")
            ->line("Amount: SAR " . number_format($amount, 2))
            ->line("Date: " . now()->format('Y-m-d H:i:s'))
            ->cc('usmansamb@gmail.com')//saadi.alami@aljeri.com
            ->bcc('usmansamb2@gmail.com');

        if (!empty($this->message)) {
            $mail->line($this->message);
        }

        return $mail->action('View Request Details', route('admin.wallet-requests.show', $this->walletRequest->id))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'wallet_request_id' => $this->walletRequest->id,
            'status' => $this->status,
            'message' => $this->message,
            'timestamp' => now()->toIso8601String()
        ];
    }
} 