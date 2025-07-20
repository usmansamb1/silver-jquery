<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StepStatus extends Model
{
    use HasFactory;

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

    // Status Constants (for backwards compatibility)
    const PENDING = 'pending';
    const APPROVED = 'approved';
    const REJECTED = 'rejected';
    const TRANSFERRED = 'transferred';
    const SKIPPED = 'skipped';

    /**
     * Get the default step statuses for seeding
     * 
     * @return array
     */
    public static function getDefaultStatuses(): array
    {
        return [
            [
                'name' => 'Pending',
                'code' => self::PENDING,
                'color' => '#FFA500',
                'description' => 'Step is pending approval',
                'is_active' => true
            ],
            [
                'name' => 'Approved',
                'code' => self::APPROVED,
                'color' => '#008000',
                'description' => 'Step has been approved',
                'is_active' => true
            ],
            [
                'name' => 'Rejected',
                'code' => self::REJECTED,
                'color' => '#FF0000',
                'description' => 'Step has been rejected',
                'is_active' => true
            ],
            [
                'name' => 'Transferred',
                'code' => self::TRANSFERRED,
                'color' => '#0000FF',
                'description' => 'Step has been transferred to another approver',
                'is_active' => true
            ],
            [
                'name' => 'Skipped',
                'code' => self::SKIPPED,
                'color' => '#808080',
                'description' => 'Step was skipped in the workflow',
                'is_active' => true
            ]
        ];
    }

    /**
     * Get all steps with this status.
     */
    public function steps()
    {
        return $this->hasMany(WalletApprovalStep::class, 'status_id');
    }
} 