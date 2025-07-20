<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class HealthController extends Controller
{
    public function check()
    {
        $status = [
            'status' => 'ok',
            'timestamp' => now()->toIso8601String(),
            'services' => []
        ];

        // Check Queue Health
        try {
            $pendingJobs = DB::table('jobs')->count();
            $failedJobs = DB::table('failed_jobs')
                ->whereDate('failed_at', '>=', now()->subDay())
                ->count();

            $status['services']['queue'] = [
                'status' => $failedJobs > 0 ? 'warning' : 'ok',
                'pending_jobs' => $pendingJobs,
                'failed_jobs_24h' => $failedJobs
            ];
        } catch (\Exception $e) {
            $status['services']['queue'] = [
                'status' => 'error',
                'message' => 'Could not check queue status'
            ];
        }

        // Check if queue worker is running
        try {
            $lastJobProcessed = Cache::get('last_job_processed');
            $workerActive = $lastJobProcessed && now()->diffInMinutes($lastJobProcessed) < 5;

            $status['services']['queue_worker'] = [
                'status' => $workerActive ? 'ok' : 'error',
                'last_processed' => $lastJobProcessed ? $lastJobProcessed->toIso8601String() : null
            ];
        } catch (\Exception $e) {
            $status['services']['queue_worker'] = [
                'status' => 'error',
                'message' => 'Could not check worker status'
            ];
        }

        // Set overall status
        if (in_array('error', array_column($status['services'], 'status'))) {
            $status['status'] = 'error';
        } elseif (in_array('warning', array_column($status['services'], 'status'))) {
            $status['status'] = 'warning';
        }

        return response()->json($status);
    }
} 