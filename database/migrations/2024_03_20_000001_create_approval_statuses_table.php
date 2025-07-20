<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('approval_statuses')) {
            Schema::create('approval_statuses', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('code')->unique();
                $table->string('color')->default('#000000');
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
            
            // Insert default statuses
            DB::table('approval_statuses')->insert([
                ['name' => 'Pending', 'code' => 'pending', 'color' => '#FFC107', 'description' => 'Approval is pending', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Approved', 'code' => 'approved', 'color' => '#4CAF50', 'description' => 'Approval is granted', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Rejected', 'code' => 'rejected', 'color' => '#F44336', 'description' => 'Approval is rejected', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }
    }

    public function down()
    {
        Schema::dropIfExists('approval_statuses');
    }
}; 