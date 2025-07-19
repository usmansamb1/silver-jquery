<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Utils\ArabicTextUtils;

class MapLocation extends Model
{
    use HasFactory;

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 
        'title',
        'latitude', 
        'longitude', 
        'kml_code', 
        'status',
        'address', 
        'description',
        'description_raw', 
        'region', 
        'city',
        'type',
        'services',
        'hours',
        'source_map_id'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'services' => 'array'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'google_maps_url',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
            
            // Generate a KML code if not provided
            if (empty($model->kml_code)) {
                $model->kml_code = $model->name . '_' . $model->latitude . '_' . $model->longitude;
            }
            
            // Ensure title is set if missing
            if (empty($model->title)) {
                $model->title = $model->name;
            }

            // Sanitize Arabic text fields
            $model->name = static::sanitizeArabicText($model->name);
            $model->title = static::sanitizeArabicText($model->title);
            $model->city = static::sanitizeArabicText($model->city);
            $model->region = static::sanitizeArabicText($model->region);
            $model->address = static::sanitizeArabicText($model->address);
            $model->description_raw = static::sanitizeArabicText($model->description_raw);
        });

        static::updating(function ($model) {
            // Sanitize Arabic text fields on update too
            $model->name = static::sanitizeArabicText($model->name);
            $model->title = static::sanitizeArabicText($model->title);
            $model->city = static::sanitizeArabicText($model->city);
            $model->region = static::sanitizeArabicText($model->region);
            $model->address = static::sanitizeArabicText($model->address);
            $model->description_raw = static::sanitizeArabicText($model->description_raw);
        });
    }

    /**
     * Sanitize Arabic text to ensure database compatibility
     *
     * @param string|null $text
     * @return string
     */
    protected static function sanitizeArabicText($text)
    {
        if (empty($text)) {
            return '';
        }

        // Use the dedicated utility class for Arabic text handling
        return ArabicTextUtils::sanitize($text);
    }

    /**
     * Get the Google Maps URL for this location
     *
     * @return string
     */
    public function getGoogleMapsUrlAttribute()
    {
        return "https://www.google.com/maps/search/?api=1&query={$this->latitude},{$this->longitude}";
    }

    /**
     * Set the description_raw attribute with proper truncation
     *
     * @param  string  $value
     * @return void
     */
    public function setDescriptionRawAttribute($value)
    {
        $sanitizedValue = static::sanitizeArabicText($value);
        $this->attributes['description_raw'] = substr($sanitizedValue, 0, 255);
    }

    /**
     * Set the services attribute.
     *
     * @param  mixed  $value
     * @return void
     */
    public function setServicesAttribute($value)
    {
        if (is_string($value)) {
            // Handle comma-separated string
            $this->attributes['services'] = json_encode(explode(',', $value));
        } else {
            $this->attributes['services'] = is_array($value) ? json_encode($value) : json_encode([]);
        }
    }

    /**
     * Get the type attribute with a default value
     * 
     * @param mixed $value
     * @return string
     */
    public function getTypeAttribute($value)
    {
        return $value ?: 'standard';
    }

    /**
     * Format status to lowercase
     *
     * @param string $value
     * @return void
     */
    public function setStatusAttribute($value)
    {
        $this->attributes['status'] = strtolower($value ?: 'unknown');
    }

    /**
     * Format coordinates for display
     * 
     * @return string
     */
    public function getCoordinatesAttribute()
    {
        return $this->latitude . ',' . $this->longitude;
    }

    /**
     * Get the status with proper formatting
     * 
     * @return string
     */
    public function getFormattedStatusAttribute()
    {
        $status = strtolower($this->status);
        
        switch ($status) {
            case 'verified':
            case 'operational':
            case 'active':
                return 'Operational';
            case 'pending':
            case 'verification required':
            case 'under check':
                return 'Pending Verification';
            case 'maintenance':
            case 'under maintenance':
                return 'Under Maintenance';
            case 'suspended':
            case 'closed':
                return 'Not Available';
            default:
                return 'Unknown';
        }
    }
} 