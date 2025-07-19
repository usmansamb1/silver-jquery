<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles
        $roles = [
            'admin',
            'finance',
            'audit',
            'it',
            'contractor',
            'customer',
            'validation',
            'delivery',
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // Assign finance role to finance@test.com
        $financeUser = User::where('email', 'finance@test.com')->first();
        if ($financeUser) {
            $financeUser->syncRoles(['finance']);
        }

        $this->command->info('Roles created successfully!');
    }
} 