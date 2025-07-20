<?php

namespace App\Notifications;

use App\Models\ApprovalInstance;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApprovalRequiredNotification extends Notification implements ShouldQueue
{
    use Queueable; 

    protected $approvalInstance;

    /**
     * Create a new notification instance.
     */
    public function __construct(ApprovalInstance $approvalInstance)
    {
        $this->approvalInstance = $approvalInstance;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = ['database'];
        
        if ($this->approvalInstance->workflow->notify_by_email) {
            $channels[] = 'mail';
        }
        
        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $approvableType = class_basename($this->approvalInstance->approvable_type);
        $currentApproval = $this->approvalInstance->currentApproval();
        $currentStep = $currentApproval ? $currentApproval->step : null;
        
        return (new MailMessage)
            ->subject("Your Approval is Required: {$approvableType} #{$this->approvalInstance->id}")
            ->greeting("Hello {$notifiable->name}!")
            ->line("Your approval is required for a {$approvableType}.")
            ->line("Current approval step: {$currentStep->name}")
            ->action('Review Now', url(route('approvals.form', $this->approvalInstance->id)))
            ->line('Thank you for your prompt attention to this matter.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $approvableType = class_basename($this->approvalInstance->approvable_type);
        $currentApproval = $this->approvalInstance->currentApproval();
        $currentStep = $currentApproval ? $currentApproval->step : null;
        
        return [
            'title' => "Your Approval is Required",
            'message' => "A {$approvableType} requires your approval.",
            'approval_instance_id' => $this->approvalInstance->id,
            'approvable_type' => $this->approvalInstance->approvable_type,
            'approvable_id' => $this->approvalInstance->approvable_id,
            'step' => $currentStep ? $currentStep->name : 'Unknown',
            'url' => route('approvals.form', $this->approvalInstance->id),
        ];
    }
} 