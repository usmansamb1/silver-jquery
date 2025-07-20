<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasUUID
{
    /**
     * Boot the trait.
     */
    protected static function bootHasUUID()
    {
        static::creating(function ($model) {
            if (!$model->uuid) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }
} 