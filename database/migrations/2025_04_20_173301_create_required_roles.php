<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create the required roles for the wallet approval process
        $roles = ['finance', 'validation', 'activation'];
        
        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName], ['name' => $roleName, 'guard_name' => 'web']);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We don't delete roles on rollback to prevent data loss
    }
};
