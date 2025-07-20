<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceOrder extends BaseModel
{
    use HasFactory;
    protected $fillable = ['user_id', 'service_type', 'payment_type', 'amount', 'file', 'notes', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
