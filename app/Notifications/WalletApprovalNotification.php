<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\WalletApprovalRequest;

class WalletApprovalNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $approvalRequest;
    protected $status;
    protected $comment;

    /**
     * Create a new notification instance.
     *
     * @param WalletApprovalRequest $approvalRequest
     * @param string $status
     * @param string|null $comment
     * @return void
     */
    public function __construct(WalletApprovalRequest $approvalRequest, $status = 'pending', ?string $comment = null)
    {
        $this->approvalRequest = $approvalRequest;
        $this->status = $status;
        $this->comment = $comment;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $message = (new MailMessage)
            ->subject($this->getSubject())
            ->greeting('Hello ' . $notifiable->name);

        switch ($this->status) {
            case 'pending':
                $message->line('A new wallet top-up request requires your approval.')
                    ->line('Details:')
                    ->line('- Customer: ' . $this->approvalRequest->payment->user->name)
                    ->line('- Amount: SAR ' . number_format($this->approvalRequest->payment->amount, 2))
                    ->line('- Payment Method: ' . ucfirst(str_replace('_', ' ', $this->approvalRequest->payment->payment_type)))
                    ->action('View Request', route('admin.wallet-requests.show', $this->approvalRequest->id))
                    ->line('Please review and take action on this request.');
                break;

            case 'completed':
                $message->line('Your wallet top-up request has been approved!')
                    ->line('Details:')
                    ->line('- Amount: SAR ' . number_format($this->approvalRequest->payment->amount, 2))
                    ->line('- Payment Method: ' . ucfirst(str_replace('_', ' ', $this->approvalRequest->payment->payment_type)))
                    ->line('The amount has been added to your wallet balance.')
                    ->action('View Wallet', route('wallet.index'));
                break;

            case 'rejected':
                $message->line('Your wallet top-up request has been rejected.')
                    ->line('Details:')
                    ->line('- Amount: SAR ' . number_format($this->approvalRequest->payment->amount, 2))
                    ->line('- Payment Method: ' . ucfirst(str_replace('_', ' ', $this->approvalRequest->payment->payment_type)))
                    ->line('- Reason: ' . ($this->comment ?? 'No reason provided'))
                    ->line('Please contact support if you need assistance.')
                    ->action('Try Again', route('wallet.topup'));
                break;
        }

        return $message->line('Thank you for using our service.');
    }

    protected function getSubject()
    {
        switch ($this->status) {
            case 'pending':
                return 'New Wallet Top-up Approval Required - JoilYaseeir';
            case 'completed':
                return 'Wallet Top-up Approved - JoilYaseeir';
            case 'rejected':
                return 'Wallet Top-up Rejected - JoilYaseeir';
            default:
                return 'Wallet Top-up Update - JoilYaseeir';
        }
    }
} 