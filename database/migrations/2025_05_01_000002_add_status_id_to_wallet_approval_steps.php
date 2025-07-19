<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::table('v2_wallet_approval_steps', function (Blueprint $table) {
            // Add status_id column if it doesn't exist
            if (!Schema::hasColumn('v2_wallet_approval_steps', 'status_id')) {
                $table->unsignedBigInteger('status_id')->nullable()->after('status');
                $table->foreign('status_id')->references('id')->on('step_statuses');
            }
        });

        // Populate the status_id based on the existing status string
        $statusMap = DB::table('step_statuses')->pluck('id', 'code')->toArray();
        
        // Only process if we have status data
        if (!empty($statusMap)) {
            // Get all steps
            $steps = DB::table('v2_wallet_approval_steps')->whereNotNull('status')->get();
            
            // Update each step
            foreach ($steps as $step) {
                $statusCode = $step->status;
                if (isset($statusMap[$statusCode])) {
                    DB::table('v2_wallet_approval_steps')
                        ->where('id', $step->id)
                        ->update(['status_id' => $statusMap[$statusCode]]);
                }
            }
        }
    }

    public function down()
    {
        Schema::table('v2_wallet_approval_steps', function (Blueprint $table) {
            // Drop foreign key if it exists
            if (Schema::hasColumn('v2_wallet_approval_steps', 'status_id')) {
                $table->dropForeign(['status_id']);
                $table->dropColumn('status_id');
            }
        });
    }
}; 