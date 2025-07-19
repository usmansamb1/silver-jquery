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
        Schema::table('service_bookings', function (Blueprint $table) {
            $table->string('service_type')->nullable()->after('service_id');
            $table->decimal('refule_amount', 10, 2)->default(0)->after('plate_number');
            $table->string('vehicle_manufacturer')->nullable()->after('vehicle_make');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_bookings', function (Blueprint $table) {
            $table->dropColumn('service_type');
            $table->dropColumn('refule_amount');
            $table->dropColumn('vehicle_manufacturer');
        });
    }
};
