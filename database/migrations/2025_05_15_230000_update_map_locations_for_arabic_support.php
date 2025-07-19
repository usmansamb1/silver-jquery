<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateMapLocationsForArabicSupport extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For MySQL, we ensure UTF8MB4 charset for Unicode/Arabic text support
        if (DB::connection()->getDriverName() === 'mysql') {
            // MySQL already supports UTF8MB4 by default in Laravel
            // Just clean up any encoding issues in existing data
            DB::statement("UPDATE map_locations SET name = REPLACE(name, '?', '') WHERE name LIKE '%?%'");
            DB::statement("UPDATE map_locations SET title = REPLACE(title, '?', '') WHERE title LIKE '%?%'");
            DB::statement("UPDATE map_locations SET city = REPLACE(city, '?', '') WHERE city LIKE '%?%'");
            DB::statement("UPDATE map_locations SET region = REPLACE(region, '?', '') WHERE region LIKE '%?%'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No downgrade as going back would risk data loss
    }
}
