<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PendingRegistration extends Model
{
    use HasFactory;
    protected $fillable = ['registration_data', 'mobile', 'otp', 'otp_created_at', 'temp_token'];
    protected $keyType = 'string';
    public $incrementing = false;

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
            // Generate a temporary token if not provided.
            if (empty($model->temp_token)) {
                $model->temp_token = Str::random(40);
            }
        });
    }
}
