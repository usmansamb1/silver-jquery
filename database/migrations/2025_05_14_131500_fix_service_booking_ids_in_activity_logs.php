<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For MySQL, we use REGEXP to check if subject_id is numeric
        // Find all activity logs for ServiceBooking where the subject_id is an integer
        $logs = DB::table('activity_logs')
            ->where('subject_type', 'App\\Models\\ServiceBooking')
            ->whereRaw("subject_id REGEXP '^[0-9]+$'")
            ->get();
        
        foreach ($logs as $log) {
            // Convert the integer ID to a UUID-compatible format
            $newUuid = sprintf('%08d-0000-0000-0000-000000000000', substr($log->subject_id, 0, 8));
            
            // Update the record
            DB::table('activity_logs')
                ->where('id', $log->id)
                ->update(['subject_id' => $newUuid]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration cannot be reversed as we don't store the original values
    }
}; 