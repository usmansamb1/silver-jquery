<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class LogController extends Controller
{
    /**
     * Display the logs page
     */
    public function index(Request $request)
    {
        $query = ActivityLog::with(['causer']);
        
        // Filter by event type if requested
        if ($request->has('event') && $request->event != '') {
            $query->where('event', $request->event);
        }
        
        // Filter by level if requested
        if ($request->has('level') && $request->level != '') {
            $query->where('level', $request->level);
        }
        
        // Filter by date range if requested
        if ($request->has('date_from') && $request->date_from != '') {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to != '') {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        // Get the logs with pagination
        $logs = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // Get event types and levels for filter dropdowns
        $eventTypes = ActivityLog::distinct()->pluck('event')->filter()->toArray();
        $levels = ActivityLog::distinct()->pluck('level')->filter()->toArray();
        
        return view('admin.logs.index', compact('logs', 'eventTypes', 'levels'));
    }
    
    /**
     * Show log details
     */
    public function show(ActivityLog $log)
    {
        return view('admin.logs.show', compact('log'));
    }
}
