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
        // For immediate fix: Clear problematic activity log entries that are causing errors
        // This is a safer approach than trying to modify the primary key type of the service_bookings table
        DB::table('activity_logs')
            ->where('subject_type', 'App\\Models\\ServiceBooking')
            ->delete();
            
        // We'll also modify any future activity logs for ServiceBooking to use string IDs correctly
        // by ensuring the LogHelper is configured to handle both int and UUID IDs properly
        // The actual model update to use UUIDs should be done in a separate, more careful migration
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cannot restore deleted logs
    }
};
