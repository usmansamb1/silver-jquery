<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\MapLocation;
use App\Utils\ArabicTextUtils;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First make sure all the columns are properly set up for Arabic
        if (DB::connection()->getDriverName() === 'sqlsrv') {
            try {
                // The maps table
                DB::statement('ALTER TABLE map_locations ALTER COLUMN name NVARCHAR(255)');
                DB::statement('ALTER TABLE map_locations ALTER COLUMN title NVARCHAR(255)');
                DB::statement('ALTER TABLE map_locations ALTER COLUMN city NVARCHAR(255)');
                DB::statement('ALTER TABLE map_locations ALTER COLUMN region NVARCHAR(255)');
                DB::statement('ALTER TABLE map_locations ALTER COLUMN address NVARCHAR(1000)');
                DB::statement('ALTER TABLE map_locations ALTER COLUMN description_raw NVARCHAR(1000)');
            } catch (\Exception $e) {
                // If we get an error, continue anyway - likely columns already altered
                Log::warning('Error altering columns: ' . $e->getMessage());
            }
        }
        
        // Then manually fix all the existing data
        try {
            // Direct SQL approach to fix question marks
            DB::statement("UPDATE map_locations SET name = '' WHERE name LIKE '%?%?%'");
            DB::statement("UPDATE map_locations SET title = '' WHERE title LIKE '%?%?%'");
            DB::statement("UPDATE map_locations SET city = '' WHERE city LIKE '%?%?%'");
            DB::statement("UPDATE map_locations SET region = '' WHERE region LIKE '%?%?%'");
        } catch (\Exception $e) {
            Log::error('Error updating question marks: ' . $e->getMessage());
        }
        
        // Direct SQL for updating Western Region specifically
        try {
            // Set proper Arabic text for regions as a baseline
            DB::statement("UPDATE map_locations SET region = N'المنطقة الغربية' WHERE region LIKE '%Western%' OR region LIKE '%?%?%' OR region = ''");
            DB::statement("UPDATE map_locations SET region = N'المنطقة الوسطى' WHERE region LIKE '%Central%'");
            DB::statement("UPDATE map_locations SET region = N'المنطقة الشرقية' WHERE region LIKE '%Eastern%'");
            DB::statement("UPDATE map_locations SET region = N'المنطقة الشمالية' WHERE region LIKE '%Northern%'");
            DB::statement("UPDATE map_locations SET region = N'المنطقة الجنوبية' WHERE region LIKE '%Southern%'");
        } catch (\Exception $e) {
            Log::error('Error updating regions: ' . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No direct way to reverse these changes
    }
}; 