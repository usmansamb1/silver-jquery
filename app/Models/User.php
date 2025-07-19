<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    //use HasApiTokens, HasFactory, Notifiable;
    use HasApiTokens, HasFactory, Notifiable, HasRoles, HasUuids, SoftDeletes; // <-- Add HasUuids and SoftDeletes
    // --- UUID Configuration ---
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;
    // --- End UUID Configuration ---

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'mobile',
        'gender',
        'registration_type',
        'region',
        'company_type',
        'company_name',
        'cr_number',
        'vat_number',
        'city',
        'building_number',
        'zip_code',
        'company_region',
        'is_active',
        'otp',
        'otp_created_at',
        'last_login_at',
        'customer_no',
        'avatar',
        'status',
        'terms_accepted_at',
        'locale',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
        'status' => 'string',
        'terms_accepted_at' => 'datetime',
    ];

    protected $dates = [
        'deleted_at',
        'last_login_at',
        'otp_created_at',
        'terms_accepted_at',
    ];

    protected $appends = [
        'formatted_customer_no',
        'avatar_url'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }

            // Set default status for new users
            if (empty($model->status)) {
                $model->status = 'active';
            }

            // Generate unique customer number if not already set
            if (empty($model->customer_no)) {
                $model->customer_no = self::generateUniqueCustomerNumber();
            }
        });
    }

    /**
     * Generate a unique customer number that will never repeat
     * 
     * Format: YYMMDDxxxxxx (12 digits)
     * - YYMMDD: Date component (6 digits)
     * - xxxxxx: Sequential number for that day (6 digits, starts from 100001)
     * 
     * This ensures:
     * - Uniqueness across time (date component)
     * - Up to 899,999 customers per day (100001-999999)
     * - Human readable and sortable
     * - Never repeats (date always moves forward)
     * 
     * @return int
     */
    private static function generateUniqueCustomerNumber(): int
    {
        $maxRetries = 10;
        $attempt = 0;

        while ($attempt < $maxRetries) {
            try {
                return DB::transaction(function () {
                    // Get current date components
                    $datePrefix = now()->format('ymd'); // YYMMDD format
                    
                    // Find the highest customer number for today
                    $todayPrefix = (int)$datePrefix;
                    $todayMin = $todayPrefix * 1000000 + 100001; // YYMMDDxxxxxx where xxxxxx starts at 100001
                    $todayMax = $todayPrefix * 1000000 + 999999; // YYMMDDxxxxxx where xxxxxx ends at 999999
                    
                    // Get the highest customer number for today with row-level locking
                    $maxCustomerToday = DB::table('users')
                        ->whereBetween('customer_no', [$todayMin, $todayMax])
                        ->lockForUpdate()
                        ->max('customer_no');
                    
                    if ($maxCustomerToday) {
                        // Increment the sequence for today
                        $nextCustomerNo = $maxCustomerToday + 1;
                        
                        // Check if we've exceeded daily limit
                        if ($nextCustomerNo > $todayMax) {
                            throw new \Exception('Daily customer number limit exceeded. Please contact system administrator.');
                        }
                    } else {
                        // First customer for today
                        $nextCustomerNo = $todayMin;
                    }
                    
                    // Double-check uniqueness (should not be necessary due to unique constraint, but good practice)
                    $exists = DB::table('users')
                        ->where('customer_no', $nextCustomerNo)
                        ->lockForUpdate()
                        ->exists();
                    
                    if ($exists) {
                        throw new \Exception('Customer number collision detected');
                    }
                    
                    return $nextCustomerNo;
                }, 5); // 5 second timeout
                
            } catch (\Exception $e) {
                $attempt++;
                
                if ($attempt >= $maxRetries) {
                    // Fallback to timestamp-based approach if daily approach fails
                    return self::generateFallbackCustomerNumber();
                }
                
                // Wait a random amount of time before retrying (1-100ms)
                usleep(rand(1000, 100000));
            }
        }
        
        // Final fallback
        return self::generateFallbackCustomerNumber();
    }

    /**
     * Fallback customer number generation using timestamp + random
     * 
     * Format: Unix timestamp (10 digits) + random 2 digits = 12 digits
     * This ensures uniqueness even if date-based approach fails
     * 
     * @return int
     */
    private static function generateFallbackCustomerNumber(): int
    {
        $maxRetries = 20;
        $attempt = 0;
        
        while ($attempt < $maxRetries) {
            // Generate customer number: timestamp (last 8 digits) + random 4 digits
            $timestamp = substr((string)time(), -8); // Last 8 digits of timestamp
            $random = str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT); // 4 random digits
            $customerNo = (int)($timestamp . $random);
            
            // Ensure it's exactly 12 digits and doesn't start with 0
            if ($customerNo < 100000000000) {
                $customerNo += 100000000000;
            }
            
            // Check if this number already exists
            $exists = DB::table('users')
                ->where('customer_no', $customerNo)
                ->exists();
            
            if (!$exists) {
                return $customerNo;
            }
            
            $attempt++;
            usleep(rand(1000, 10000)); // Wait 1-10ms before retry
        }
        
        // Ultimate fallback: use microtime
        $microtime = (int)(microtime(true) * 1000000);
        return $microtime % 999999999999 + 100000000000; // Ensure 12 digits
    }

    /**
     * Accessor to display the customer number with leading zeros (12-digit formatted).
     *
     * Usage: $user->formatted_customer_no
     */
    public function getFormattedCustomerNoAttribute()
    {
        return $this->customer_no ? str_pad($this->customer_no, 12, '0', STR_PAD_LEFT) : null;
    }

    /**
     * Accessor to get the avatar URL with fallback to default image.
     *
     * Usage: $user->avatar_url
     */
    public function getAvatarUrlAttribute()
    {
        if ($this->avatar && Storage::disk('public')->exists($this->avatar)) {
            return asset('storage/' . $this->avatar);
        }
        
        // Return default avatar based on user type
        return 'https://cdn.iconscout.com/icon/premium/png-256-thumb/saudi-man-5742049-4804571.png';
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function serviceOrders()
    {
        return $this->hasMany(ServiceOrder::class);
    }

    /**
     * Get the saved cards for the user.
     */
    public function savedCards()
    {
        return $this->hasMany(SavedCard::class);
    }

    /**
     * Get the status histories for the user.
     */
    public function statusHistories()
    {
        return $this->morphMany(StatusHistory::class, 'model');
    }

    /**
     * Format the last_login_at attribute for display
     *
     * @return string|null
     */
    public function getFormattedLastLoginAttribute()
    {
        if (!$this->last_login_at) {
            return 'N/A';
        }
        
        try {
            return $this->last_login_at->diffForHumans();
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    /**
     * Route notifications for the SMS channel.
     *
     * @return string
     */
    public function routeNotificationForSms()
    {
        return $this->mobile;
    }
}
