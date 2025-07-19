<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class LogsController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::where('causer_id', Auth::id())
            ->where('log_name', 'user');
            
        // Apply filters
        if ($request->has('event') && !empty($request->event)) {
            $query->where('event', $request->event);
        }
        
        if ($request->has('date_from') && !empty($request->date_from)) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && !empty($request->date_to)) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $logs = $query->orderBy('created_at', 'desc')
            ->paginate(15);
            
        $eventTypes = ActivityLog::where('causer_id', Auth::id())
            ->where('log_name', 'user')
            ->distinct('event')
            ->pluck('event')
            ->toArray();
            
        return view('user.logs.index', compact('logs', 'eventTypes'));
    }
    
    public function show($id)
    {
        $log = ActivityLog::where('causer_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();
            
        return view('user.logs.show', compact('log'));
    }
} 