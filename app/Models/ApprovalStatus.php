<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApprovalStatus extends Model
{
    protected $fillable = [
        'name',
        'code',
        'color',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    // Status Constants
    const PENDING = 'pending';
    const APPROVED = 'approved';
    const REJECTED = 'rejected';
    const IN_PROGRESS = 'in_progress';
    const CANCELLED = 'cancelled';

    public static function getDefaultStatuses(): array
    {
        return [
            [
                'name' => 'Pending',
                'code' => self::PENDING,
                'color' => '#FFA500',
                'description' => 'Waiting for approval',
                'is_active' => true
            ],
            [
                'name' => 'Approved',
                'code' => self::APPROVED,
                'color' => '#008000',
                'description' => 'Request has been approved',
                'is_active' => true
            ],
            [
                'name' => 'Rejected',
                'code' => self::REJECTED,
                'color' => '#FF0000',
                'description' => 'Request has been rejected',
                'is_active' => true
            ],
            [
                'name' => 'In Progress',
                'code' => self::IN_PROGRESS,
                'color' => '#0000FF',
                'description' => 'Request is being processed',
                'is_active' => true
            ],
            [
                'name' => 'Cancelled',
                'code' => self::CANCELLED,
                'color' => '#808080',
                'description' => 'Request has been cancelled',
                'is_active' => true
            ]
        ];
    }

    public function requests()
    {
        return $this->hasMany(WalletApprovalRequest::class, 'status_id');
    }
} 