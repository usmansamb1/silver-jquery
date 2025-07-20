<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Vehicle extends Model
{
    use HasFactory, HasUuids;
    
    protected $fillable = [
        'user_id',
        'plate_number', 
        'make', 
        'manufacturer', 
        'model', 
        'year',
        'rfid_number',
        'rfid_balance',
        'rfid_status',
        'status'
    ];
    
    /**
     * Get the user that owns the vehicle.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the service bookings associated with the vehicle.
     */
    public function serviceBookings()
    {
        return $this->hasMany(ServiceBooking::class);
    }
    
    /**
     * Get the RFID recharge transactions for this vehicle.
     */
    public function rfidTransactions()
    {
        return $this->hasMany(RfidTransaction::class);
    }
    
    /**
     * Check if the vehicle has an RFID chip assigned.
     */
    public function hasRfid()
    {
        return !empty($this->rfid_number);
    }
    
    /**
     * Get the formatted RFID balance.
     */
    public function getFormattedRfidBalanceAttribute()
    {
        return 'SAR ' . number_format($this->rfid_balance ?? 0, 2);
    }
    
    /**
     * Get the status label for the RFID.
     */
    public function getRfidStatusLabelAttribute()
    {
        $statuses = [
            'active' => '<span class="badge bg-success">Active</span>',
            'inactive' => '<span class="badge bg-warning">Inactive</span>',
            'pending' => '<span class="badge bg-secondary">Pending</span>',
            'suspended' => '<span class="badge bg-danger">Suspended</span>'
        ];
        
        return $statuses[$this->rfid_status] ?? '<span class="badge bg-light">None</span>';
    }
    
    /**
     * Get the status label for the vehicle.
     */
    public function getStatusLabelAttribute()
    {
        $statuses = [
            'active' => '<span class="badge bg-success">Active</span>',
            'inactive' => '<span class="badge bg-warning">Inactive</span>',
            'pending_delivery' => '<span class="badge bg-info">Pending Delivery</span>',
            'delivered' => '<span class="badge bg-primary">Delivered</span>'
        ];
        
        return $statuses[$this->status] ?? '<span class="badge bg-light">None</span>';
    }
    
    /**
     * Get the latest service booking for this vehicle.
     */
    public function getLatestServiceBookingAttribute()
    {
        return $this->serviceBookings()
            ->orderBy('created_at', 'desc')
            ->first();
    }
}
