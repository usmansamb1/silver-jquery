<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RfidTransfer extends Model
{
    use HasFactory, HasUuids;
    
    protected $fillable = [
        'user_id',
        'source_vehicle_id',
        'target_vehicle_id',
        'rfid_number',
        'otp_code',
        'otp_expires_at',
        'verified_at',
        'status',
        'notes',
        'transfer_details'
    ];
    
    protected $casts = [
        'otp_expires_at' => 'datetime',
        'verified_at' => 'datetime',
        'transfer_details' => 'array'
    ];
    
    /**
     * Get the user who initiated the transfer.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the source vehicle.
     */
    public function sourceVehicle()
    {
        return $this->belongsTo(Vehicle::class, 'source_vehicle_id');
    }
    
    /**
     * Get the target vehicle.
     */
    public function targetVehicle()
    {
        return $this->belongsTo(Vehicle::class, 'target_vehicle_id');
    }
    
    /**
     * Check if the OTP has expired.
     */
    public function isOtpExpired()
    {
        return now()->gt($this->otp_expires_at);
    }
    
    /**
     * Check if the transfer has been verified.
     */
    public function isVerified()
    {
        return !is_null($this->verified_at);
    }
    
    /**
     * Get the status label for the transfer.
     */
    public function getStatusLabelAttribute()
    {
        $statuses = [
            'pending' => '<span class="badge bg-warning">Pending</span>',
            'completed' => '<span class="badge bg-success">Completed</span>',
            'cancelled' => '<span class="badge bg-danger">Cancelled</span>',
            'verified' => '<span class="badge bg-info">Verified</span>'
        ];
        
        return $statuses[$this->status] ?? '<span class="badge bg-secondary">Unknown</span>';
    }
} 