<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'token',
        'otp',
        'purpose',
        'data',
        'expires_at',
        'is_used',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'expires_at' => 'datetime',
        'is_used' => 'boolean',
        'data' => 'array',
        'otp' => 'string',
    ];

    /**
     * Accessor to ensure OTP is always returned as a string
     * This helps with strict comparison during verification
     *
     * @param mixed $value
     * @return string
     */
    public function getOtpAttribute($value)
    {
        return (string) $value;
    }

    /**
     * Find an active OTP by token and value.
     *
     * @param string $token
     * @param string $otp
     * @return \App\Models\Otp|null
     */
    public static function findActiveByTokenAndOtp($token, $otp)
    {
        return static::where('token', $token)
            ->where('otp', (string) $otp)
            ->where('expires_at', '>=', now())
            ->where('is_used', false)
            ->first();
    }

    /**
     * Find an active OTP by mobile and value.
     *
     * @param string $mobile
     * @param string $otp
     * @param string $purpose
     * @return \App\Models\Otp|null
     */
    public static function findActiveByMobileAndOtp($mobile, $otp, $purpose = 'login')
    {
        return static::where('otp', (string) $otp)
            ->where('purpose', $purpose)
            ->where('expires_at', '>=', now())
            ->where('is_used', false)
            ->whereJsonContains('data->mobile', $mobile)
            ->first();
    }
} 