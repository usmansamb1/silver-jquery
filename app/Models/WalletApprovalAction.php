<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WalletApprovalAction extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'request_id',
        'step_id',
        'user_id',
        'action',
        'comment',
        'attachment',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
        'request_id' => 'string',
        'user_id' => 'string'
    ];

    public function request()
    {
        return $this->belongsTo(WalletApprovalRequest::class, 'request_id', 'id');
    }

    public function step()
    {
        return $this->belongsTo(WalletApprovalStep::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getActionColorAttribute()
    {
        return match($this->action) {
            'approve' => '#28a745',
            'reject' => '#dc3545',
            'transfer' => '#17a2b8',
            default => '#6c757d'
        };
    }

    public function getActionIconAttribute()
    {
        return match($this->action) {
            'approve' => 'fas fa-check',
            'reject' => 'fas fa-times',
            'transfer' => 'fas fa-exchange-alt',
            default => 'fas fa-question'
        };
    }

    public function getFormattedCreatedAtAttribute()
    {
        return $this->created_at->format('Y-m-d H:i:s');
    }

    public function getAttachmentUrlAttribute()
    {
        return $this->attachment ? asset('storage/' . $this->attachment) : null;
    }

    /**
     * Get the metadata attribute.
     *
     * @param  string|null  $value
     * @return array|null
     */
    public function getMetadataAttribute($value)
    {
        if (is_null($value)) {
            return null;
        }
        
        if (is_array($value)) {
            return $value;
        }
        
        return json_decode($value, true);
    }

    /**
     * Set the metadata attribute.
     *
     * @param  array|null  $value
     * @return void
     */
    public function setMetadataAttribute($value)
    {
        if (is_null($value)) {
            $this->attributes['metadata'] = null;
            return;
        }
        
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $value = $decoded;
            }
        }
        
        $this->attributes['metadata'] = is_array($value) ? json_encode($value) : $value;
    }
} 