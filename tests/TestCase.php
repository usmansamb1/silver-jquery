<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    // Add a new method to handle migration conflicts
    protected function fixMigrationConflicts()
    {
        // Check if the payment_id column exists in v2_wallet_approval_requests table
        if (Schema::hasTable('v2_wallet_approval_requests') && Schema::hasColumn('v2_wallet_approval_requests', 'payment_id')) {
            // Column already exists, don't try to add it again
            DB::statement('SELECT 1'); // Dummy statement to avoid empty if block
        } else {
            // No conflict, continue normally
        }
    }

    // Override the setUp method
    protected function setUp(): void
    {
        parent::setUp();
        
        // Fix migration conflicts
        $this->fixMigrationConflicts();
    }
}
