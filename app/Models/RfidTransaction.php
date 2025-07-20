<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RfidTransaction extends Model
{
    use HasFactory, HasUuids;
    
    protected $fillable = [
        'vehicle_id',
        'user_id',
        'amount',
        'payment_method',
        'transaction_reference',
        'status',
        'payment_status',
        'transaction_details',
        'hyperpay_transaction_id',
    ];
    
    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_details' => 'array',
    ];
    
    /**
     * Get the vehicle associated with this transaction.
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
    
    /**
     * Get the user who performed this transaction.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the wallet transaction if paid via wallet.
     */
    public function walletTransaction()
    {
        return $this->morphOne(Transaction::class, 'transactionable');
    }
    
    /**
     * Get the formatted amount.
     */
    public function getFormattedAmountAttribute()
    {
        return 'SAR ' . number_format($this->amount, 2);
    }
} 