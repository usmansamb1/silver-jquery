<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->decimal('base_price', 10, 2);
            $table->boolean('is_active')->default(true);
            $table->enum('service_type', ['A', 'B']);
            $table->integer('estimated_duration')->comment('in minutes');
            $table->decimal('vat_percentage', 5, 2)->default(15.00);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('services');
    }
}; 