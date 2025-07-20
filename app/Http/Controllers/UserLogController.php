<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserLogController extends Controller
{
    /**
     * Display the user's activity logs
     */
    public function index(Request $request)
    {
        // Get the authenticated user
        $user = Auth::user();
        
        // Query logs related to this user (where the user is the causer)
        $query = ActivityLog::where('causer_id', $user->id)
                ->where('causer_type', get_class($user));
        
        // Filter by event type if requested
        if ($request->has('event') && $request->event != '') {
            $query->where('event', $request->event);
        }
        
        // Filter by date range if requested
        if ($request->has('date_from') && $request->date_from != '') {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to != '') {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        // Get the logs with pagination
        $logs = $query->orderBy('created_at', 'desc')->paginate(10);
        
        // Get event types for filter dropdown
        $eventTypes = ActivityLog::where('causer_id', $user->id)
                    ->where('causer_type', get_class($user))
                    ->distinct()
                    ->pluck('event')
                    ->filter()
                    ->toArray();
        
        return view('user.logs.index', compact('logs', 'eventTypes'));
    }
    
    /**
     * Show specific log details
     */
    public function show(Request $request, $id)
    {
        $user = Auth::user();
        
        // Find the log and make sure it belongs to the authenticated user
        $log = ActivityLog::where('id', $id)
                ->where('causer_id', $user->id)
                ->where('causer_type', get_class($user))
                ->firstOrFail();
        
        return view('user.logs.show', compact('log'));
    }
}
