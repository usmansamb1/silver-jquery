<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Wallet extends Model
{
    use HasFactory, HasUuids;
    
    public $incrementing = false;
    protected $keyType = 'string';
    
    protected $fillable = ['user_id', 'balance'];
    
    protected $casts = [
        'balance' => 'decimal:2',
    ];

    /**
     * Get the user that owns the wallet.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the transactions for the wallet.
     */
    public function transactions()
    {
        return $this->hasMany(WalletTransaction::class)->orderBy('created_at', 'desc');
    }
    
    /**
     * Record a transaction to the wallet.
     *
     * @param float $amount
     * @param string $type
     * @param string $description
     * @param Model|null $reference
     * @param array $metadata
     * @return WalletTransaction
     */
    public function recordTransaction($amount, $type, $description = null, $reference = null, $metadata = [])
    {
        return DB::transaction(function () use ($amount, $type, $description, $reference, $metadata) {
            // Refresh the wallet to get the latest balance
            $this->refresh();
            
            $balanceBefore = $this->balance;
            
            // Update the balance based on the transaction type
            if (in_array($type, ['deposit', 'refund'])) {
                $this->balance += $amount;
            } else {
                // For withdrawals, service payments, etc.
                if ($amount > $this->balance) {
                    throw new \Exception('Insufficient funds');
                }
                $this->balance -= $amount;
            }
            
            $this->save();
            
            // Create the transaction record
            $transaction = new WalletTransaction([
                'wallet_id' => $this->id,
                'user_id' => $this->user_id,
                'amount' => $amount,
                'type' => $type,
                'status' => 'completed',
                'description' => $description,
                'balance_before' => $balanceBefore,
                'balance_after' => $this->balance,
                'metadata' => $metadata,
            ]);
            
            // Set the reference if provided
            if ($reference) {
                $transaction->reference_type = get_class($reference);
                $transaction->reference_id = $reference->getKey();
            } else {
                // If no reference is provided, set a default reference to user model
                // This prevents NULL values in non-nullable columns
                $transaction->reference_type = get_class($this->user);
                $transaction->reference_id = $this->user_id;
            }
            
            $transaction->save();
            
            return $transaction;
        });
    }
    
    /**
     * Deposit money into the wallet.
     *
     * @param float $amount
     * @param string $description
     * @param Model|null $reference
     * @param array $metadata
     * @return WalletTransaction
     */
    public function deposit($amount, $description = null, $reference = null, $metadata = [])
    {
        return $this->recordTransaction($amount, 'deposit', $description, $reference, $metadata);
    }
    
    /**
     * Withdraw money from the wallet.
     *
     * @param float $amount
     * @param string $description
     * @param Model|null $reference
     * @param array $metadata
     * @return WalletTransaction
     * @throws \Exception
     */
    public function withdraw($amount, $description = null, $reference = null, $metadata = [])
    {
        return $this->recordTransaction($amount, 'withdrawal', $description, $reference, $metadata);
    }
    
    /**
     * Pay for a service from the wallet.
     *
     * @param float $amount
     * @param string $description
     * @param Model|null $reference
     * @param array $metadata
     * @return WalletTransaction
     * @throws \Exception
     */
    public function payForService($amount, $description = null, $reference = null, $metadata = [])
    {
        return $this->recordTransaction($amount, 'service_payment', $description, $reference, $metadata);
    }
    
    /**
     * Process a refund to the wallet.
     *
     * @param float $amount
     * @param string $description
     * @param Model|null $reference
     * @param array $metadata
     * @return WalletTransaction
     */
    public function refund($amount, $description = null, $reference = null, $metadata = [])
    {
        return $this->recordTransaction($amount, 'refund', $description, $reference, $metadata);
    }
    
    /**
     * Get the formatted balance with currency.
     */
    public function getFormattedBalanceAttribute()
    {
        $sarIcon = '<span class="icon-saudi_riyal" style="color: black;"></span>';
        return number_format($this->balance, 2) . ' ' . $sarIcon . '';
    }
}
