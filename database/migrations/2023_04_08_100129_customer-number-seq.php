<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       // DB::statement("CREATE SEQUENCE dbo.customer_no_seq AS BIGINT START WITH 1 INCREMENT BY 1");
        Schema::table('users', function ($table) {
           // $table->unsignedBigInteger('customer_no')->unique()->nullable()->after('id');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP SEQUENCE dbo.customer_no_seq");

    }
};
