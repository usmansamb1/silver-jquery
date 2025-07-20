<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Uuids;

class V2WalletApprovalRequest extends Model
{
    use HasFactory, SoftDeletes, Uuids;

    protected $table = 'v2_wallet_approval_requests';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'payment_id',
        'user_id',
        'status',
        'current_step',
        'amount',
        'reference_no',
        'rejection_reason',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    // Relationship with payment
    public function payment()
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }

    // Relationship with user
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
