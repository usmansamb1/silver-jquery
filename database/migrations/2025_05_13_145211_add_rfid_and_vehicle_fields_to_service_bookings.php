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
            $table->uuid('vehicle_id')->nullable()->after('user_id');
            $table->string('rfid_number')->nullable()->after('reference_number');
            $table->enum('delivery_status', ['pending', 'delivered'])->default('pending')->after('status');
            
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_bookings', function (Blueprint $table) {
            $table->dropForeign(['vehicle_id']);
            $table->dropColumn(['vehicle_id', 'rfid_number', 'delivery_status']);
        });
    }
};
