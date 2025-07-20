<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class SavedCard extends Model
{
    use HasFactory, HasUuids;
    
    public $incrementing = false;
    protected $keyType = 'string';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'card_brand',
        'last_four',
        'expiry_month',
        'expiry_year',
        'stripe_payment_method_id',
        'is_default'
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'expiry_month' => 'integer',
        'expiry_year' => 'integer',
        'is_default' => 'boolean'
    ];
    
    /**
     * Get the user that owns the saved card.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Format the expiration date as MM/YY.
     *
     * @return string
     */
    public function getFormattedExpiryAttribute()
    {
        return sprintf('%02d/%d', $this->expiry_month, substr($this->expiry_year, -2));
    }
    
    /**
     * Get masked version of the card number.
     *
     * @return string
     */
    public function getMaskedNumberAttribute()
    {
        return '**** **** **** ' . $this->last_four;
    }
    
    /**
     * Get card type with last four digits.
     *
     * @return string
     */
    public function getCardIdentifierAttribute()
    {
        return ucfirst($this->card_brand) . ' ending in ' . $this->last_four;
    }
} 