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
        // First check if there are any existing service_bookings records
        $hasRecords = DB::table('service_bookings')->count() > 0;
        
        if ($hasRecords) {
            // Delete existing bookings that might have string service_ids (which cause SQL errors)
            // Only in development/testing environments
            if (app()->environment('local', 'testing')) {
                // Convert SQL Server ISNUMERIC to MySQL equivalent
                DB::statement("DELETE FROM service_bookings WHERE service_id NOT REGEXP '^[0-9]+$'");
            }
        }
        
        // Check if services table has records, if not, create some basic services
        $servicesCount = DB::table('services')->count();
        if ($servicesCount === 0) {
            // Create basic services
            DB::table('services')->insert([
                [
                    'name' => 'RFID Chip for Trucks Size 80mm',
                    'description' => 'RFID chip installation for cars (80mm)',
                    'base_price' => 150.00,
                    'is_active' => 1,
                    'service_type' => 'A',
                    'estimated_duration' => 30,
                    'vat_percentage' => 15.00,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'name' => 'RFID Chip for Trucks Size 120mm',
                    'description' => 'RFID chip installation for trucks (120mm)',
                    'base_price' => 200.00,
                    'is_active' => 1,
                    'service_type' => 'A',
                    'estimated_duration' => 45,
                    'vat_percentage' => 15.00,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'name' => 'Oil Change Service',
                    'description' => 'Complete oil change service',
                    'base_price' => 120.00,
                    'is_active' => 1,
                    'service_type' => 'B',
                    'estimated_duration' => 60,
                    'vat_percentage' => 15.00,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to rollback
    }
};
