<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations to convert service_bookings table from auto-increment to UUID.
     */
    public function up(): void
    {
        // MySQL compatible steps for converting an identity column to UUID
        
        // Step 1: Create a temporary backup of the table before modification
        try {
            DB::statement("CREATE TABLE service_bookings_backup AS SELECT * FROM service_bookings");
        } catch (Exception $e) {
            // Backup table might already exist, continue
        }
        
        // Step 2: Create a new temporary table with the desired UUID structure
        // The new table will not have identity column for id, but rather a proper UUID column
        if (!Schema::hasTable('service_bookings_new')) {
            Schema::create('service_bookings_new', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('user_id');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreignId('service_id')->nullable();
                $table->string('vehicle_make')->nullable();
                $table->string('vehicle_model')->nullable();
                $table->string('vehicle_year')->nullable();
                $table->string('plate_number')->nullable();
                $table->date('booking_date')->nullable();
                $table->time('booking_time')->nullable();
                $table->decimal('base_price', 10, 2)->nullable();
                $table->decimal('vat_amount', 10, 2)->nullable();
                $table->decimal('total_amount', 10, 2)->nullable();
                $table->string('payment_method')->nullable();
                $table->string('payment_status')->default('pending');
                $table->string('status')->default('pending');
                $table->string('reference_number')->nullable();
                $table->uuid('order_id')->nullable();
                $table->string('service_type')->nullable();
                $table->string('vehicle_manufacturer')->nullable();
                $table->decimal('refule_amount', 10, 2)->nullable();
                $table->uuid('vehicle_id')->nullable();
                $table->string('pickup_location')->nullable();
                $table->string('rfid_number')->nullable();
                $table->string('delivery_status')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }
        
        // Step 3: Try to copy data from the old table to the new one
        // First, we need to generate UUIDs for each record in the old table
        $records = DB::table('service_bookings')->get();
        foreach ($records as $record) {
            // Create a UUID for each record
            $uuid = Str::uuid()->toString();
            
            // Set all column values we want to preserve
            $values = [
                'id' => $uuid,
                'user_id' => $record->user_id ?? null,
                'service_id' => $record->service_id ?? null,
                'vehicle_make' => $record->vehicle_make ?? null,
                'vehicle_model' => $record->vehicle_model ?? null,
                'vehicle_year' => $record->vehicle_year ?? null,
                'plate_number' => $record->plate_number ?? null,
                'booking_date' => $record->booking_date ?? null,
                'booking_time' => $record->booking_time ?? null,
                'base_price' => $record->base_price ?? null,
                'vat_amount' => $record->vat_amount ?? null,
                'total_amount' => $record->total_amount ?? null,
                'payment_method' => $record->payment_method ?? null,
                'payment_status' => $record->payment_status ?? null,
                'status' => $record->status ?? $record->booking_status ?? 'pending',
                'reference_number' => $record->reference_number ?? null,
                'order_id' => $record->order_id ?? null,
                'service_type' => $record->service_type ?? null,
                'vehicle_manufacturer' => $record->vehicle_manufacturer ?? null,
                'refule_amount' => $record->refule_amount ?? null,
                'vehicle_id' => $record->vehicle_id ?? null,
                'pickup_location' => $record->pickup_location ?? null,
                'rfid_number' => $record->rfid_number ?? null,
                'delivery_status' => $record->delivery_status ?? null,
                'created_at' => $record->created_at,
                'updated_at' => $record->updated_at,
                'deleted_at' => $record->deleted_at ?? null
            ];
            
            // Insert into the new table
            DB::table('service_bookings_new')->insert($values);
            
            // Update any related records in other tables that reference this service_booking
            // For example, update orders or activity logs that reference this booking ID
            // This step is optional and depends on your specific database relationships
        }
        
        // Step 4: Drop the old table and rename the new one
        Schema::drop('service_bookings');
        Schema::rename('service_bookings_new', 'service_bookings');
        
        // Step 5: Recreate any indexes or constraints that were on the original table
        Schema::table('service_bookings', function (Blueprint $table) {
            // Add any missing indexes from the original table
            $table->index('user_id');
            $table->index('service_id');
            $table->index('order_id');
            $table->index('reference_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // MySQL implementation - this is not easily reversible
        // For fresh migrations, this rollback is not needed
        echo "WARNING: This migration rollback is not fully supported for MySQL.\n";
        echo "This migration was designed for converting existing data.\n";
        echo "For fresh installations, simply run migrate:fresh instead.\n";
    }
}; 