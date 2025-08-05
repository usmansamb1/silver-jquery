<?php

namespace App\Notifications;

use App\Models\ServiceBooking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RfidDeliveryNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $booking;

    /**
     * Create a new notification instance.
     *
     * @param ServiceBooking $booking
     * @return void
     */
    public function __construct(ServiceBooking $booking)
    {
        $this->booking = $booking;
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
        $vehicle = $this->booking->vehicle_manufacturer . ' ' . $this->booking->vehicle_make . ' ' . $this->booking->vehicle_model;
        
        return (new MailMessage)
            ->subject('RFID Delivery Confirmation , FuelApp - JOIL')
            ->greeting('Dear ' . $notifiable->name)
            ->line('We are pleased to inform you that your RFID has been successfully delivered and activated for your vehicle.')
            ->line('RFID Details:')
            ->line('- RFID Number: ' . $this->booking->rfid_number)
            ->line('- Vehicle: ' . $vehicle)
            ->line('- Plate Number: ' . $this->booking->plate_number)
            ->line('- Service Reference: ' . $this->booking->reference_number)
            ->line('You can now use your RFID for contactless fueling and other services at our stations.')
            ->action('View Your Account', url('/services/booking/history'))
            ->line('If you have any questions about your RFID, please contact our customer support.')
            ->line('Thank you for choosing FuelApp - JOIL')
            ->cc('usmansamb@gmail.com')//saadi.alami@aljeri.com
            ->bcc('usmansamb2@gmail.com');
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
            'booking_id' => $this->booking->id,
            'reference_number' => $this->booking->reference_number,
            'rfid_number' => $this->booking->rfid_number,
            'message' => 'Your RFID has been delivered and activated',
        ];
    }
} 