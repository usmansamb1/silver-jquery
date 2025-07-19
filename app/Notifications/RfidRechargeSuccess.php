<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Vehicle;

class RfidRechargeSuccess extends Notification implements ShouldQueue
{
    use Queueable;

    public $vehicle;
    public $amount;
    public $paymentReference;
    public $cardBrand;

    /**
     * Create a new notification instance.
     */
    public function __construct(Vehicle $vehicle, $amount, $paymentReference, $cardBrand = null)
    {
        $this->vehicle = $vehicle;
        $this->amount = $amount;
        $this->paymentReference = $paymentReference;
        $this->cardBrand = $cardBrand;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $subject = 'RFID Recharge Successful - ' . $this->vehicle->plate_number;
        
        $message = (new MailMessage)
            ->subject($subject)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Your RFID recharge has been completed successfully.')
            ->line('**Vehicle Details:**')
            ->line('• Vehicle: ' . $this->vehicle->manufacturer . ' ' . $this->vehicle->make . ' ' . $this->vehicle->model)
            ->line('• Plate Number: ' . $this->vehicle->plate_number)
            ->line('• RFID Number: ' . $this->vehicle->rfid_number)
            ->line('')
            ->line('**Transaction Details:**')
            ->line('• Recharged Amount: SAR ' . number_format($this->amount, 2))
            ->line('• New RFID Balance: SAR ' . number_format($this->vehicle->rfid_balance, 2))
            ->line('• Payment Reference: ' . $this->paymentReference);

        if ($this->cardBrand) {
            $message->line('• Payment Method: Credit Card (' . $this->cardBrand . ')');
        } else {
            $message->line('• Payment Method: Wallet');
        }

        $message->line('• Transaction Date: ' . now()->format('d M Y, H:i'))
            ->line('')
            ->line('Your RFID is now ready to use at our partner fuel stations.')
            ->action('View RFID Management', route('rfid.index'))
            ->line('Thank you for using JoilYaseeir!');

        return $message;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'rfid_recharge_success',
            'vehicle_id' => $this->vehicle->id,
            'plate_number' => $this->vehicle->plate_number,
            'rfid_number' => $this->vehicle->rfid_number,
            'amount' => $this->amount,
            'new_balance' => $this->vehicle->rfid_balance,
            'payment_reference' => $this->paymentReference,
            'card_brand' => $this->cardBrand,
            'transaction_date' => now()->toISOString(),
        ];
    }
}