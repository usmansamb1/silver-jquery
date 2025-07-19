<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

final class SmsLog extends Model
{
    use HasFactory, HasUuids;

    /**
     * Indicates if the IDs are auto-incrementing.
     */
    public $incrementing = false;

    /**
     * The data type of the auto-incrementing ID.
     */
    protected $keyType = 'string';

    protected $fillable = [
        'mobile',
        'message',
        'provider',
        'status',
        'purpose',
        'reference_id',
        'request_data',
        'response_data',
        'error_message',
        'retry_count',
        'sent_at',
        'failed_at',
    ];

    protected $casts = [
        'request_data' => 'array',
        'response_data' => 'array',
        'sent_at' => 'datetime',
        'failed_at' => 'datetime',
        'retry_count' => 'integer',
    ];

    /**
     * Scope for pending SMS
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for sent SMS
     */
    public function scopeSent(Builder $query): Builder
    {
        return $query->where('status', 'sent');
    }

    /**
     * Scope for failed SMS
     */
    public function scopeFailed(Builder $query): Builder
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope for specific purpose
     */
    public function scopeByPurpose(Builder $query, string $purpose): Builder
    {
        return $query->where('purpose', $purpose);
    }

    /**
     * Scope for specific mobile number
     */
    public function scopeByMobile(Builder $query, string $mobile): Builder
    {
        return $query->where('mobile', $mobile);
    }

    /**
     * Mark SMS as sent
     */
    public function markAsSent(array $responseData = []): void
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now(),
            'response_data' => $responseData,
        ]);
    }

    /**
     * Mark SMS as failed
     */
    public function markAsFailed(string $errorMessage, array $responseData = []): void
    {
        $this->update([
            'status' => 'failed',
            'failed_at' => now(),
            'error_message' => $errorMessage,
            'response_data' => $responseData,
        ]);
    }

    /**
     * Increment retry count
     */
    public function incrementRetry(): void
    {
        $this->increment('retry_count');
        $this->update(['status' => 'retry']);
    }

    /**
     * Get SMS statistics
     */
    public static function getStatistics(): array
    {
        return [
            'total' => self::count(),
            'sent' => self::sent()->count(),
            'failed' => self::failed()->count(),
            'pending' => self::pending()->count(),
            'success_rate' => self::count() > 0 ? round((self::sent()->count() / self::count()) * 100, 2) : 0,
            'today_sent' => self::sent()->whereDate('sent_at', today())->count(),
            'today_failed' => self::failed()->whereDate('failed_at', today())->count(),
        ];
    }

    /**
     * Get SMS statistics by purpose
     */
    public static function getStatisticsByPurpose(): array
    {
        return self::selectRaw('purpose, COUNT(*) as total, 
                              SUM(CASE WHEN status = "sent" THEN 1 ELSE 0 END) as sent,
                              SUM(CASE WHEN status = "failed" THEN 1 ELSE 0 END) as failed')
            ->groupBy('purpose')
            ->get()
            ->keyBy('purpose')
            ->toArray();
    }

    /**
     * Check if SMS can be retried
     */
    public function canRetry(int $maxRetries = 3): bool
    {
        return $this->retry_count < $maxRetries && $this->status !== 'sent';
    }

    /**
     * Get formatted mobile number
     */
    public function getFormattedMobileAttribute(): string
    {
        return preg_replace('/(\d{3})(\d{3})(\d{4})/', '$1-$2-$3', $this->mobile);
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'sent' => 'success',
            'failed' => 'danger',
            'pending' => 'warning',
            'retry' => 'info',
            default => 'secondary'
        };
    }

    /**
     * Get provider display name
     */
    public function getProviderDisplayNameAttribute(): string
    {
        return match($this->provider) {
            'connectsaudi' => 'ConnectSaudi',
            default => ucfirst($this->provider)
        };
    }
} 