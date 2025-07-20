<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\StepStatus;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('step_statuses')) {
            Schema::create('step_statuses', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('code')->unique();
                $table->string('color')->default('#000000');
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
            
            // Insert default statuses using the model's method
            foreach (StepStatus::getDefaultStatuses() as $status) {
                DB::table('step_statuses')->insert(array_merge(
                    $status, 
                    ['created_at' => now(), 'updated_at' => now()]
                ));
            }
        }
    }

    public function down()
    {
        Schema::dropIfExists('step_statuses');
    }
}; 