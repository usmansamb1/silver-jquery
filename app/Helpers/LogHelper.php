<?php

namespace App\Helpers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;

class LogHelper
{
    /**
     * Log a user activity
     *
     * @param string $event The event type (login, logout, wallet_recharge, etc.)
     * @param string $description A description of the activity
     * @param ?Model $subject The subject entity (optional)
     * @param array $properties Additional properties (optional)
     * @param string $level Log level (default: info)
     * @return ActivityLog
     */
    public static function log(
        string $event,
        string $description,
        ?Model $subject = null,
        array $properties = [],
        string $level = 'info'
    ): ActivityLog {
        // For ServiceBooking models, use the specialized safe method to avoid UUID issues
        if ($subject && get_class($subject) === 'App\\Models\\ServiceBooking') {
            return self::safeLogServiceBooking($subject, $description, $properties);
        }
        
        $request = request();
        
        // Create context data for logging
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
        
        // Log using Laravel's logging system
        Log::$level($description, $context);
        
        // Create the database log entry
        $log = new ActivityLog();
        $log->log_name = 'system';
        $log->description = $description;
        $log->event = $event;
        $log->level = $level;
        
        // Set subject if provided
        if ($subject) {
            // Check if subject ID is a valid UUID format for SQL Server compatibility
            $subjectId = (string)$subject->getKey();
            if (!empty($subjectId) && preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $subjectId)) {
                $log->subject_id = $subjectId;
                $log->subject_type = get_class($subject);
            } else {
                // Skip setting subject_id/subject_type if not a valid UUID
                // Instead, include information in properties
                $properties['related_model_type'] = get_class($subject);
                $properties['related_model_id'] = $subjectId;
            }
        }
        
        // Set causer (the user who performed the action)
        if (Auth::check()) {
            $log->causer_id = Auth::id();
            $log->causer_type = get_class(Auth::user());
        }
        
        // Add IP and User Agent
        $log->ip_address = $request->ip();
        $log->user_agent = $request->userAgent();
        
        // Fix any invalid or empty UUID strings in the properties
        if (isset($properties['booking_id']) && (empty($properties['booking_id']) || !preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $properties['booking_id']))) {
            // Either remove the booking_id or set it to a valid UUID format
            unset($properties['booking_id']);
        }
        
        // Add properties
        $log->properties = $context;
        
        try {
            $log->save();
        } catch (\Exception $e) {
            // If save fails, likely due to UUID conversion issue, 
            // create a record without problematic subject relationships
            \Illuminate\Support\Facades\Log::error('Failed to save activity log', [
                'error' => $e->getMessage(),
                'event' => $event,
                'description' => $description
            ]);
            
            // Try again without subject information
            $log->subject_id = null;
            $log->subject_type = null;
            $log->save();
        }
        
        return $log;
    }
    
    /**
     * Log a login event
     *
     * @param User $user
     * @param string $description
     * @param array $properties
     * @return ActivityLog
     */
    public static function logLogin(User $user, string $description = 'User logged in', array $properties = []): ActivityLog
    {
        return self::log('login', $description, $user, $properties);
    }
    
    /**
     * Log a logout event
     *
     * @param User $user
     * @param string $description
     * @param array $properties
     * @return ActivityLog
     */
    public static function logLogout(User $user, string $description = 'User logged out', array $properties = []): ActivityLog
    {
        return self::log('logout', $description, $user, $properties);
    }
    
    /**
     * Log an OTP verification event
     *
     * @param User $user
     * @param string $description
     * @param array $properties
     * @return ActivityLog
     */
    public static function logOtpVerification(User $user, string $description = 'OTP verified successfully', array $properties = []): ActivityLog
    {
        return self::log('otp_verification', $description, $user, $properties);
    }
    
    /**
     * Log a wallet recharge event
     *
     * @param Model $wallet The wallet or transaction model
     * @param string $description
     * @param array $properties
     * @return ActivityLog
     */
    public static function logWalletRecharge(Model $wallet, string $description, array $properties = []): ActivityLog
    {
        return self::log('wallet_recharge', $description, $wallet, $properties);
    }
    
    /**
     * Log a service booking event
     *
     * @param Model $booking The service booking model
     * @param string $description
     * @param array $properties
     * @return ActivityLog
     */
    public static function logServiceBooking(Model $booking, string $description, array $properties = []): ActivityLog
    {
        return self::log('service_booking', $description, $booking, $properties);
    }
    
    /**
     * Log an approval action event
     *
     * @param Model $approval The approval model
     * @param string $action The approval action (approve, reject)
     * @param string $description
     * @param array $properties
     * @return ActivityLog
     */
    public static function logApprovalAction(Model $approval, string $action, string $description, array $properties = []): ActivityLog
    {
        $properties['approval_action'] = $action;
        return self::log('approval_action', $description, $approval, $properties);
    }
    
    /**
     * Log a profile update event
     *
     * @param User $user
     * @param string $description
     * @param array $properties
     * @return ActivityLog
     */
    public static function logProfileUpdate(User $user, string $description = 'Profile updated', array $properties = []): ActivityLog
    {
        return self::log('profile_update', $description, $user, $properties);
    }

    /**
     * Log a wallet submission event
     *
     * @param Model $submission The wallet submission model
     * @param string $description
     * @param array $properties
     * @return ActivityLog
     */
    public static function logWalletSubmission(Model $submission, string $description, array $properties = []): ActivityLog
    {
        return self::log('wallet_submission', $description, $submission, $properties);
    }
        
    /**
     * Log a generic activity
     *
     * @param string $description
     * @param array $context
     * @param string $level
     * @return ActivityLog
     */
    public static function activity(string $description, array $context = [], string $level = 'info'): ActivityLog
    {
        $event = $context['type'] ?? 'general';
        $subject = $context['subject'] ?? null;
        unset($context['type'], $context['subject']);
        
        return self::log($event, $description, $subject, $context, $level);
    }

    /**
     * Log a service booking RFID delivery event - safely handling integer IDs
     *
     * @param Model $booking The service booking model
     * @param string $description
     * @param array $properties
     * @return ActivityLog|null
     */
    public static function logServiceRfidDelivery(Model $booking, string $description, array $properties = []): ?ActivityLog
    {
        try {
            // Safely log by cloning properties but using NULL for subject to avoid ID type issues
            $activityLog = new ActivityLog();
            $activityLog->log_name = 'system';
            $activityLog->description = $description;
            $activityLog->event = 'service_booking';
            $activityLog->level = 'info';
            
            // Skip subject association but include it in properties
            $properties['booking_ref'] = $booking->reference_number ?? 'N/A';
            $properties['booking_id_str'] = (string)$booking->id;
            
            // Set causer (the user who performed the action)
            if (auth()->check()) {
                $activityLog->causer_id = auth()->id();
                $activityLog->causer_type = get_class(auth()->user());
            }
            
            $request = request();
            // Add properties
            $activityLog->properties = array_merge([
                'type' => 'service_booking',
                'database' => true,
                'subject' => $booking,
                'route' => $request->route() ? $request->route()->getName() : null,
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ], $properties);
            
            // Add IP and User Agent
            $activityLog->ip_address = $request->ip();
            $activityLog->user_agent = $request->userAgent();
            
            $activityLog->save();
            
            return $activityLog;
        } catch (\Exception $e) {
            // Log the error but don't crash the application
            \Illuminate\Support\Facades\Log::error("Error logging service RFID delivery: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Safely log a service booking event with proper UUID handling
     * 
     * @param Model $booking The service booking model
     * @param string $description
     * @param array $properties
     * @return ActivityLog
     */
    public static function safeLogServiceBooking(Model $booking, string $description, array $properties = []): ActivityLog
    {
        // Remove booking_id from properties to avoid UUID conversion errors
        if (isset($properties['booking_id'])) {
            unset($properties['booking_id']);
        }
        
        // Add reference number instead for tracking
        $properties['booking_reference'] = $booking->reference_number ?? null;
        
        // Create an alternative string ID that won't cause conversion errors
        $properties['booking_id_safe'] = (string)$booking->id;
        
        // Create the database log entry directly to handle UUID type properly
        $log = new ActivityLog();
        $log->log_name = 'system';
        $log->description = $description;
        $log->event = 'service_booking';
        $log->level = 'info';
        
        $request = request();
        
        // Set causer (the user who performed the action)
        if (Auth::check()) {
            $log->causer_id = Auth::id();
            $log->causer_type = get_class(Auth::user());
        }
        
        // Skip subject association entirely to avoid UUID conversion issues
        // DO NOT set subject_id/subject_type for ServiceBooking to avoid SQL Server UUID issues
        
        // Add IP and User Agent
        $log->ip_address = $request->ip();
        $log->user_agent = $request->userAgent();
        
        // Build context without the subject
        $context = array_merge([
            'type' => 'service_booking',
            'database' => true,
            'route' => $request->route() ? $request->route()->getName() : null,
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ], $properties);
        
        // Add important booking data explicitly
        $context['booking_data'] = [
            'reference_number' => $booking->reference_number ?? null,
            'service_type' => $booking->service_type ?? null,
            'service_id' => $booking->service_id ?? null,
            'vehicle_make' => $booking->vehicle_make ?? null,
            'payment_status' => $booking->payment_status ?? null,
            'status' => $booking->status ?? null
        ];
        
        // Add properties
        $log->properties = $context;
        
        // Save without subject relation
        $log->save();
        
        // Log using Laravel's logging system as well
        Log::info($description, $context);
        
        return $log;
    }
} 