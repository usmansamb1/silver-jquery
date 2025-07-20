<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use App\Services\NotificationService;

class SmsChannel
{
    /**
     * The notification service instance.
     *
     * @var \App\Services\NotificationService
     */
    protected $notificationService;

    /**
     * Create a new SMS channel instance.
     *
     * @param  \App\Services\NotificationService  $notificationService
     * @return void
     */
    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        if (!$notifiable->routeNotificationFor('sms') && !$notifiable->mobile) {
            return;
        }

        $message = $notification->toSms($notifiable);

        if (empty($message)) {
            return;
        }
        
        // Get the mobile number using the routeNotificationFor method or the mobile attribute
        $mobile = $notifiable->routeNotificationFor('sms') ?: $notifiable->mobile;
        
        // Send the SMS through the notification service
        $this->notificationService->sendSms($mobile, $message, 'otp');
    }
} 