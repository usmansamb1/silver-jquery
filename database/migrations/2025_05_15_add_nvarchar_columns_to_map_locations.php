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
        // For SQL Server, we need to use NVARCHAR for Unicode/Arabic text
        if (config('database.default') === 'sqlsrv') {
            // First backup any existing data
            $backupData = DB::table('map_locations')->get();
            
            // Check if we should use ALTER TABLE (preferred) or recreate
            if (count($backupData) > 0) {
                // Alter each column to use NVARCHAR
                DB::statement('ALTER TABLE map_locations ALTER COLUMN name NVARCHAR(255)');
                DB::statement('ALTER TABLE map_locations ALTER COLUMN title NVARCHAR(255)');
                DB::statement('ALTER TABLE map_locations ALTER COLUMN city NVARCHAR(255)');
                DB::statement('ALTER TABLE map_locations ALTER COLUMN region NVARCHAR(255)');
                DB::statement('ALTER TABLE map_locations ALTER COLUMN address NVARCHAR(1000)');
                DB::statement('ALTER TABLE map_locations ALTER COLUMN description_raw NVARCHAR(1000)');
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Nothing to reverse as we can't convert back without data loss
    }
}; 