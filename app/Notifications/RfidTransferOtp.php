<?php

namespace App\Notifications;

use App\Models\RfidTransfer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Channels\SmsChannel;

class RfidTransferOtp extends Notification implements ShouldQueue
{
    use Queueable;

    protected $transfer;
    protected $otpCode;

    /**
     * Create a new notification instance.
     */
    public function __construct(RfidTransfer $transfer, $otpCode)
    {
        $this->transfer = $transfer;
        $this->otpCode = $otpCode;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database', SmsChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $sourceVehicle = $this->transfer->sourceVehicle;
        $targetVehicle = $this->transfer->targetVehicle;
        
        return (new MailMessage)
            ->subject('RFID Transfer OTP Verification - Joil Yaseeir')
            ->greeting('Hello ' . $notifiable->name)
            ->line('You have requested to transfer an RFID chip from one vehicle to another.')
            ->line('Source Vehicle: ' . $sourceVehicle->manufacturer . ' ' . $sourceVehicle->make . ' ' . $sourceVehicle->model . ' (' . $sourceVehicle->plate_number . ')')
            ->line('Target Vehicle: ' . $targetVehicle->manufacturer . ' ' . $targetVehicle->make . ' ' . $targetVehicle->model . ' (' . $targetVehicle->plate_number . ')')
            ->line('Your OTP code is: ' . $this->otpCode)
            ->line('This code will expire in 10 minutes.')
            ->action('Verify Transfer', route('rfid.verify-transfer', $this->transfer->id))
            ->line('If you did not request this transfer, please ignore this email.')
            ->cc('usmansamb@gmail.com')//saadi.alami@aljeri.com
            ->bcc('usmansamb2@gmail.com');
    }
    
    /**
     * Get the SMS representation of the notification.
     */
    public function toSms(object $notifiable): string
    {
        return 'Your JoilYaseeir RFID transfer OTP is: ' . $this->otpCode . '. This code will expire in 10 minutes.';
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'transfer_id' => $this->transfer->id,
            'source_vehicle_id' => $this->transfer->source_vehicle_id,
            'target_vehicle_id' => $this->transfer->target_vehicle_id,
            'rfid_number' => $this->transfer->rfid_number,
            'otp_expires_at' => $this->transfer->otp_expires_at->toIso8601String(),
        ];
    }
} 