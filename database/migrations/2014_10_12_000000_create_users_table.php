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
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary(); // Use UUID as primary key
            $table->enum('registration_type', ['personal', 'company']);
            // common fields
            $table->string('email')->nullable();
            $table->string('mobile');
            $table->string('otp')->nullable(); // to store OTP temporarily
            $table->timestamp('otp_created_at')->nullable();
            // personal fields
            $table->string('name')->nullable();
            $table->string('region')->nullable();
            // company fields
            $table->enum('company_type', ['private', 'semi Govt.', 'Govt'])->nullable();
            $table->string('company_name')->nullable();
            $table->string('cr_number')->nullable();
            $table->string('vat_number')->nullable();
            $table->string('city')->nullable();
            $table->string('building_number')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('company_region')->nullable();
            $table->string('password')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
