<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Str;
use Exception;

class ServiceBooking extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The data type of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'service_id',
        'order_id',
        'service_type',
        'vehicle_id',
        'vehicle_make',
        'vehicle_manufacturer',
        'vehicle_model',
        'vehicle_year',
        'plate_number',
        'refule_amount',
        'booking_date',
        'booking_time',
        'base_price',
        'vat_amount',
        'total_amount',
        'pickup_location',
        'payment_method', // 'wallet' or 'credit_card'
        'payment_status', // 'pending', 'paid', 'failed'
        'status', // 'pending', 'confirmed', 'completed', 'cancelled' (changed from booking_status)
        'reference_number',
        'rfid_number',
        'delivery_status'
    ];

    protected $casts = [
        'booking_date' => 'date',
        'booking_time' => 'datetime',
        'base_price' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'refule_amount' => 'decimal:2'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the vehicle associated with this service booking.
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Generate a UUID when creating a new ServiceBooking.
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            try {
                // Generate UUID for the ID if not already set
                if (empty($model->id)) {
                    $model->id = Str::uuid()->toString();
                }
                
                // Generate reference number if not set
                if (empty($model->reference_number)) {
                    $model->reference_number = 'SB-' . strtoupper(uniqid());
                }
            } catch (Exception $e) {
                // Log the error but don't crash during creation
                \Illuminate\Support\Facades\Log::error('Error generating UUID for ServiceBooking: ' . $e->getMessage());
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'id';
    }
} 