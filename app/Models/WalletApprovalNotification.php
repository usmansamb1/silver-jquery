<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WalletApprovalNotification extends Model
{
    protected $fillable = [
        'request_id',
        'user_id',
        'type',
        'status',
        'message',
        'sent_at',
        'read_at'
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'read_at' => 'datetime'
    ];

    public function request()
    {
        return $this->belongsTo(WalletApprovalRequest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function markAsSent()
    {
        $this->update(['sent_at' => now(), 'status' => 'sent']);
    }

    public function markAsRead()
    {
        $this->update(['read_at' => now()]);
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => '#ffc107',
            'sent' => '#17a2b8',
            'failed' => '#dc3545',
            default => '#6c757d'
        };
    }

    public function getTypeIconAttribute()
    {
        return match($this->type) {
            'email' => 'fas fa-envelope',
            'sms' => 'fas fa-sms',
            default => 'fas fa-bell'
        };
    }

    public function getIsReadAttribute()
    {
        return !is_null($this->read_at);
    }

    public function getIsSentAttribute()
    {
        return !is_null($this->sent_at);
    }
} 