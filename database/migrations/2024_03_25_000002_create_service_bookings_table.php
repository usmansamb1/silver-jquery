<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('service_bookings')) {
            Schema::create('service_bookings', function (Blueprint $table) {
                $table->id();
                $table->uuid('user_id');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreignId('service_id')->constrained()->onDelete('cascade');
                $table->string('vehicle_make');
                $table->string('vehicle_model');
                $table->year('vehicle_year');
                $table->string('plate_number');
                $table->date('booking_date');
                $table->time('booking_time');
                $table->decimal('base_price', 10, 2);
                $table->decimal('vat_amount', 10, 2);
                $table->decimal('total_amount', 10, 2);
                $table->enum('payment_method', ['wallet', 'credit_card']);
                $table->enum('payment_status', ['pending', 'paid', 'failed'])->default('pending');
                $table->enum('booking_status', ['pending', 'confirmed', 'completed', 'cancelled'])->default('pending');
                $table->string('reference_number')->unique();
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('service_bookings');
    }
}; 