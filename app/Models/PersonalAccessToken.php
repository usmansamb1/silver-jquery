<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;
use Illuminate\Support\Str;

class PersonalAccessToken extends SanctumPersonalAccessToken
{
    // Set the key type to string and disable auto-incrementing
//    protected $keyType = 'string';
//    public $incrementing = false;

    use HasUuids; // <-- Use the UUID trait

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id'; // Explicitly state it, though HasUuids often infers

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string'; // <-- Set key type to string for UUID

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false; // <-- Tell Eloquent IDs are not auto-incrementing
   /* protected static function boot()
    {
        parent::boot();

        // Auto-generate a UUID when a new token is created
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }*/
}
