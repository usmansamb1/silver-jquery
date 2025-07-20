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
        // First check if service_bookings table exists
        if (!Schema::hasTable('service_bookings')) {
            Schema::create('service_bookings', function (Blueprint $table) {
                $table->id();
                $table->uuid('user_id');
                $table->foreignId('service_id'); // Will add foreign key constraint later when services table exists
                $table->string('vehicle_make')->nullable();
                $table->string('vehicle_model')->nullable();
                $table->year('vehicle_year')->nullable();
                $table->string('plate_number')->nullable();
                $table->date('booking_date')->nullable();
                $table->time('booking_time')->nullable();
                $table->string('status')->default('pending');
                $table->decimal('base_price', 10, 2)->default(0);
                $table->decimal('vat_amount', 10, 2)->default(0);
                $table->decimal('total_amount', 10, 2)->default(0);
                $table->string('pickup_location')->nullable();
                $table->enum('payment_method', ['wallet', 'credit_card'])->default('wallet');
                $table->enum('payment_status', ['pending', 'paid', 'failed'])->default('pending');
                $table->string('reference_number')->nullable()->unique();
                $table->timestamps();
                $table->softDeletes();
            });
            
            // Don't try to add columns since we just created the table
            return;
        }
        
        // If table exists, add the columns if they don't exist
        Schema::table('service_bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('service_bookings', 'pickup_location')) {
                $table->string('pickup_location')->nullable()->after('total_amount');
            }
            
            if (!Schema::hasColumn('service_bookings', 'status')) {
                $table->string('status')->default('pending')->after('booking_time');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_bookings', function (Blueprint $table) {
            if (Schema::hasColumn('service_bookings', 'pickup_location')) {
                $table->dropColumn('pickup_location');
            }
            
            if (Schema::hasColumn('service_bookings', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
}; 