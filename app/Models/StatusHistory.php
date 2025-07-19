<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class StatusHistory extends Model
{
    use HasFactory, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'model_id',
        'model_type',
        'status_from',
        'status_to',
        'previous_status',
        'new_status',
        'notes',
        'comment',
        'user_id',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'metadata' => 'array',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();
        
        // Map previous_status to status_from and new_status to status_to for compatibility
        static::creating(function ($model) {
            if (isset($model->previous_status) && !isset($model->status_from)) {
                $model->status_from = $model->previous_status;
            }
            
            if (isset($model->new_status) && !isset($model->status_to)) {
                $model->status_to = $model->new_status;
            }
            
            if (isset($model->comment) && !isset($model->notes)) {
                $model->notes = $model->comment;
            }
        });
    }

    /**
     * Get the parent model that owns the status history.
     */
    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the user who made the status change.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the from status
     */
    public function fromStatus()
    {
        return $this->belongsTo(ApprovalStatus::class, 'status_from', 'code');
    }

    /**
     * Get the to status
     */
    public function toStatus()
    {
        return $this->belongsTo(ApprovalStatus::class, 'status_to', 'code');
    }
    
    /**
     * Get previous_status attribute (for backward compatibility)
     */
    public function getPreviousStatusAttribute()
    {
        return $this->status_from;
    }
    
    /**
     * Get new_status attribute (for backward compatibility)
     */
    public function getNewStatusAttribute()
    {
        return $this->status_to;
    }
    
    /**
     * Get comment attribute (for backward compatibility)
     */
    public function getCommentAttribute()
    {
        return $this->notes;
    }
} 