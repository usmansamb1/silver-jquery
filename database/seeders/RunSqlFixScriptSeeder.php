<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class RunSqlFixScriptSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            // Only run this for SQL Server
            if (config('database.default') === 'sqlsrv') {
                $this->command->info('Fixing IDENTITY_INSERT for service_bookings table...');
                
                // Execute SQL directly to enable IDENTITY_INSERT
                DB::unprepared("
                    -- First check if the table has an identity column
                    IF EXISTS (
                        SELECT * FROM sys.identity_columns 
                        WHERE OBJECT_NAME(object_id) = 'service_bookings'
                    )
                    BEGIN
                        -- Enable IDENTITY_INSERT for the service_bookings table 
                        PRINT 'Enabling IDENTITY_INSERT for service_bookings table'
                        SET IDENTITY_INSERT [service_bookings] ON
                        
                        -- IMPORTANT: You need to keep this setting ON until a permanent fix is applied
                        -- Remember that only ONE table can have IDENTITY_INSERT ON at a time
                        -- This is a temporary solution until the migration is run
                        
                        PRINT 'IDENTITY_INSERT is now ON for service_bookings table'
                    END
                    ELSE
                    BEGIN
                        PRINT 'The service_bookings table does not have an identity column or it is already a UUID column'
                    END
                ");
                
                $this->command->info('IDENTITY_INSERT fix applied successfully');
            } else {
                $this->command->info('Skipping IDENTITY_INSERT fix as this is not a SQL Server database (MySQL does not require IDENTITY_INSERT)');
            }
        } catch (\Exception $e) {
            $this->command->error('Error running SQL fix script: ' . $e->getMessage());
        }
    }
} 