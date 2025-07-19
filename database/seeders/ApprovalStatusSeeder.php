<?php

namespace Database\Seeders;

use App\Models\ApprovalStatus;
use Illuminate\Database\Seeder;

class ApprovalStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (ApprovalStatus::getDefaultStatuses() as $status) {
            ApprovalStatus::updateOrCreate(
                ['code' => $status['code']],
                $status
            );
        }

        $this->command->info('Default approval statuses seeded successfully.');
    }
} 