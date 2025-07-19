<?php

namespace App\Services;

use App\Jobs\SendSmsNotification;
use App\Jobs\SendEmailNotification;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Send an SMS notification
     *
     * @param string $mobile
     * @param string $message
     * @param string $purpose
     * @param string $priority
     * @return void
     */
    public function sendSms(string $mobile, string $message, string $purpose = 'general', string $priority = 'default')
    {
        try {
            SendSmsNotification::dispatch($mobile, $message, $purpose)
                ->onQueue($this->getQueuePriority($purpose, $priority));

            Log::info('SMS notification queued', [
                'mobile' => $mobile,
                'purpose' => $purpose,
                'priority' => $priority
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to queue SMS notification', [
                'mobile' => $mobile,
                'purpose' => $purpose,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Send an email notification
     *
     * @param string $email
     * @param string $subject
     * @param string $template
     * @param array $data
     * @param string $purpose
     * @param string $priority
     * @return void
     */
    public function sendEmail(
        string $email, 
        string $subject, 
        string $template, 
        array $data, 
        string $purpose = 'general', 
        string $priority = 'default'
    ) {
        try {
            SendEmailNotification::dispatch($email, $subject, $template, $data, $purpose)
                ->onQueue($this->getQueuePriority($purpose, $priority));

            Log::info('Email notification queued', [
                'email' => $email,
                'purpose' => $purpose,
                'priority' => $priority
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to queue email notification', [
                'email' => $email,
                'purpose' => $purpose,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Send bulk notifications
     *
     * @param array $notifications
     * @return void
     */
    public function sendBulk(array $notifications)
    {
        foreach ($notifications as $notification) {
            try {
                if ($notification['type'] === 'sms') {
                    $this->sendSms(
                        $notification['mobile'],
                        $notification['message'],
                        $notification['purpose'] ?? 'bulk',
                        'low'
                    );
                } elseif ($notification['type'] === 'email') {
                    $this->sendEmail(
                        $notification['email'],
                        $notification['subject'],
                        $notification['template'],
                        $notification['data'],
                        $notification['purpose'] ?? 'bulk',
                        'low'
                    );
                }
            } catch (\Exception $e) {
                Log::error('Failed to queue bulk notification', [
                    'notification' => $notification,
                    'error' => $e->getMessage()
                ]);
                // Continue with next notification even if one fails
                continue;
            }
        }
    }

    /**
     * Get queue priority based on notification purpose
     *
     * @param string $purpose
     * @param string $priority
     * @return string
     */
    protected function getQueuePriority(string $purpose, string $priority): string
    {
        // OTP and critical notifications always get high priority
        if (in_array($purpose, ['otp', 'critical'])) {
            return 'high';
        }

        // Bulk notifications always get low priority
        if ($purpose === 'bulk') {
            return 'low';
        }

        return $priority;
    }
} 