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
        Schema::create('status_histories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('model_type');
            $table->uuid('model_id');
            $table->string('status_from');
            $table->string('status_to');
            $table->uuid('user_id')->nullable();
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['model_type', 'model_id']);
            $table->index(['status_from', 'status_to']);
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('status_histories');
    }
}; 