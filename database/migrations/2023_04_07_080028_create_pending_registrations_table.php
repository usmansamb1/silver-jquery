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
        Schema::create('pending_registrations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->json('registration_data'); // store registration fields as JSON
            $table->string('mobile'); // for quick lookup/validation
            $table->string('otp');
            $table->timestamp('otp_created_at')->nullable();
            $table->string('temp_token')->unique(); // a temporary token to reference this registration
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pending_registrations');
    }
};
