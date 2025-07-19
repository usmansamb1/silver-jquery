<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLogService
{
    /**
     * Log user activity
     *
     * @param string $event Event type (login, wallet_recharge, service_booking, profile_update)
     * @param string $description Description of the activity
     * @param mixed|null $subject Related model instance
     * @param array $properties Additional properties to store
     * @param string $level Log level (info, warning, error)
     * @return ActivityLog
     */
    public static function log(string $event, string $description, $subject = null, array $properties = [], string $level = 'info'): ActivityLog
    {
        $log = new ActivityLog();
        $log->log_name = 'user';
        $log->event = $event;
        $log->description = $description;
        $log->level = $level;
        $log->ip_address = Request::ip();
        $log->user_agent = Request::userAgent();
        
        // Set causer (user who performed the action)
        if (Auth::check()) {
            $log->causer_type = get_class(Auth::user());
            $log->causer_id = Auth::id();
        }
        
        // Set related subject if provided
        if ($subject) {
            $log->subject_type = get_class($subject);
            $log->subject_id = $subject->getKey();
        }
        
        // Store additional properties
        $log->properties = $properties;
        
        $log->save();
        
        return $log;
    }
    
    /**
     * Log system activity (no user required)
     *
     * @param string $event Event type
     * @param string $description Description of the activity
     * @param array $properties Additional properties to store
     * @param string $level Log level (info, warning, error)
     * @return ActivityLog
     */
    public static function logSystem(string $event, string $description, array $properties = [], string $level = 'info'): ActivityLog
    {
        $log = new ActivityLog();
        $log->log_name = 'system';
        $log->event = $event;
        $log->description = $description;
        $log->level = $level;
        $log->ip_address = Request::ip();
        $log->user_agent = Request::userAgent();
        $log->properties = $properties;
        
        $log->save();
        
        return $log;
    }
    
    /**
     * Log login failure
     *
     * @param string $description
     * @param string $reason Reason for failure (user_not_found, invalid_credentials, etc.)
     * @param array $properties
     * @return ActivityLog
     */
    public static function logLoginFailed(string $description, string $reason, array $properties = []): ActivityLog
    {
        $mergedProperties = array_merge([
            'type' => 'login_failed',
            'database' => true,
            'subject' => null,
            'route' => request()->route() ? request()->route()->getName() : null,
            'url' => request()->fullUrl(),
            'method' => request()->method(),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'reason' => $reason
        ], $properties);
        
        return static::logSystem('login_failed', $description, $mergedProperties, 'warning');
    }
    
    /**
     * Log login activity
     *
     * @param string $description
     * @param array $properties
     * @return ActivityLog
     */
    public static function logLogin(string $description = 'User logged in', array $properties = []): ActivityLog
    {
        return static::log('login', $description, Auth::user(), $properties);
    }
    
    /**
     * Log wallet recharge activity
     *
     * @param mixed $wallet Wallet model instance
     * @param string $description
     * @param array $properties
     * @return ActivityLog
     */
    public static function logWalletRecharge($wallet, string $description, array $properties = []): ActivityLog
    {
        return static::log('wallet_recharge', $description, $wallet, $properties);
    }
    
    /**
     * Log service booking activity
     *
     * @param mixed $booking ServiceBooking model instance
     * @param string $description
     * @param array $properties
     * @return ActivityLog
     */
    public static function logServiceBooking($booking, string $description, array $properties = []): ActivityLog
    {
        return static::log('service_booking', $description, $booking, $properties);
    }
    
    /**
     * Log profile update activity
     *
     * @param string $description
     * @param array $properties
     * @return ActivityLog
     */
    public static function logProfileUpdate(string $description = 'Profile information updated', array $properties = []): ActivityLog
    {
        return static::log('profile_update', $description, Auth::user(), $properties);
    }
} 