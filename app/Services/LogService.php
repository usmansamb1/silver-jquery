<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LogService
{
    /**
     * Log an activity both to the database and Laravel's log system
     *
     * @param string $event The event type: login, wallet_recharge, service_booking, profile_update
     * @param string $description A description of the activity
     * @param mixed $subject The subject entity (optional)
     * @param array $properties Additional properties (optional)
     * @param string $level Log level (default: info)
     * @return \App\Models\ActivityLog
     */
    public static function log(
        string $event,
        string $description,
        $subject = null,
        array $properties = [],
        string $level = 'info'
    ): ActivityLog {
        $request = request();
        
        // Create context data for Laravel logging
        $context = array_merge([
            'type' => $event,
            'database' => true,
            'subject' => $subject,
            'route' => $request->route() ? $request->route()->getName() : null,
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ], $properties);
        
        // Log using Laravel's logging system as well
        Log::channel('database')->$level($description, $context);
        
        // Create and return the database log entry
        $log = new ActivityLog();
        $log->log_name = 'system';
        $log->description = $description;
        $log->event = $event;
        $log->level = $level;
        
        // Set subject if provided
        if ($subject) {
            $log->subject_id = $subject->getKey();
            $log->subject_type = get_class($subject);
        }
        
        // Set causer (the user who performed the action)
        if (Auth::check()) {
            $log->causer_id = Auth::id();
            $log->causer_type = get_class(Auth::user());
        }
        
        // Add IP and User Agent
        $log->ip_address = $request->ip();
        $log->user_agent = $request->userAgent();
        
        // Add properties
        $log->properties = $context;
        
        $log->save();
        
        return $log;
    }
    
    /**
     * Log a login event
     *
     * @param \App\Models\User $user
     * @param string $description
     * @param array $properties
     * @return \App\Models\ActivityLog
     */
    public static function logLogin($user, $description = 'User logged in', $properties = [])
    {
        return self::log('login', $description, $user, $properties);
    }
    
    /**
     * Log a wallet recharge event
     *
     * @param mixed $wallet The wallet or transaction model
     * @param string $description
     * @param array $properties
     * @return \App\Models\ActivityLog
     */
    public static function logWalletRecharge($wallet, $description, $properties = [])
    {
        return self::log('wallet_recharge', $description, $wallet, $properties);
    }
    
    /**
     * Log a service booking event
     *
     * @param mixed $booking The service booking model
     * @param string $description
     * @param array $properties
     * @return \App\Models\ActivityLog
     */
    public static function logServiceBooking($booking, $description, $properties = [])
    {
        return self::log('service_booking', $description, $booking, $properties);
    }
    
    /**
     * Log a profile update event
     *
     * @param \App\Models\User $user
     * @param string $description
     * @param array $properties
     * @return \App\Models\ActivityLog
     */
    public static function logProfileUpdate($user, $description, $properties = [])
    {
        return self::log('profile_update', $description, $user, $properties);
    }
    
    /**
     * Generic method to log any activity
     *
     * @param string $description
     * @param array $context
     * @param string $level
     * @return \App\Models\ActivityLog
     */
    public static function activity($description, array $context = [], $level = 'info')
    {
        $event = $context['type'] ?? 'general';
        $subject = $context['subject'] ?? null;
        unset($context['type'], $context['subject']);
        
        return self::log($event, $description, $subject, $context, $level);
    }
} 