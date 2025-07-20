<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    use HasFactory, HasUuids;

    /**
     * The transaction types.
     */
    const TRANSACTION_TYPES = [
        'deposit' => 'Deposit',
        'withdrawal' => 'Withdrawal',
        'transfer' => 'Transfer',
        'service_payment' => 'Service Payment',
        'refund' => 'Refund',
        'adjustment' => 'Adjustment'
    ];

    protected $fillable = [
        'wallet_id',
        'user_id',
        'reference_id',
        'reference_type',
        'amount',
        'type',
        'status',
        'description',
        'balance_before',
        'balance_after',
        'metadata'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'metadata' => 'array',
    ];

    /**
     * Get the wallet that owns the transaction.
     */
    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    /**
     * Get the user that owns the transaction.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the related model (polymorphic).
     */
    public function reference()
    {
        return $this->morphTo();
    }

    /**
     * Get the metadata as an array.
     */
    public function getMetadataAttribute($value)
    {
        if (empty($value)) {
            return [];
        }
        
        return is_string($value) ? json_decode($value, true) : $value;
    }

    /**
     * Set the metadata attribute.
     */
    public function setMetadataAttribute($value)
    {
        $this->attributes['metadata'] = is_array($value) ? json_encode($value) : $value;
    }

    /**
     * Determine if the transaction is a deposit.
     */
    public function isDeposit()
    {
        return $this->type === 'deposit';
    }

    /**
     * Determine if the transaction is a withdrawal.
     */
    public function isWithdrawal()
    {
        return $this->type === 'withdrawal';
    }

    /**
     * Determine if the transaction is a service payment.
     */
    public function isServicePayment()
    {
        return $this->type === 'service_payment';
    }

    /**
     * Get formatted amount with sign.
     */
    public function getFormattedAmountAttribute()
    {
        $sign = in_array($this->type, ['deposit', 'refund']) ? '+' : '-';
        return $sign . number_format($this->amount, 2);
    }

    /**
     * Get status badge color.
     */
    public function getStatusColorAttribute()
    {
        return [
            'completed' => 'success',
            'pending' => 'warning',
            'failed' => 'danger',
            'reversed' => 'secondary',
        ][$this->status] ?? 'primary';
    }
} 