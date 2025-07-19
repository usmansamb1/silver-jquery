<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    { 
        Schema::table('map_locations', function (Blueprint $table) {
           $table->string('name')->ifNotExists()->nullable();
           $table->string('kml_code')->ifNotExists()->nullable(); 
           $table->string('address')->ifNotExists()->nullable();
           $table->string('description_raw')->ifNotExists()->nullable();
           $table->string('region')->ifNotExists()->nullable();
           $table->string('city')->ifNotExists()->nullable();
           $table->string('station_name_extended')->ifNotExists()->nullable();
           $table->string('raw_placemark_name')->ifNotExists()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('map_locations', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->dropColumn('latitude');
            $table->dropColumn('longitude');
            $table->dropColumn('kml_code');
            $table->dropColumn('status');
        });
    }
};
