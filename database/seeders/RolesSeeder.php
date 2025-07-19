<?php
/**
 * Created by PhpStorm.
 * User: UsmanNawaz
 * Date: 4/7/2025
*/

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    public function run()
    {
        $roles = ['admin', 'finance', 'audit', 'it', 'contractor', 'customer', 'joil-validation', 'joil-activation'];

        foreach ($roles as $role) {
            Role::firstOrCreate([
                'name' => $role,
                'guard_name' => 'web'
            ]);
        }
    }
}