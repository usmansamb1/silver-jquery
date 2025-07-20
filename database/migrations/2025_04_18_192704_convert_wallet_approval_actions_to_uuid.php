<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $isSqlServer = Config::get('database.default') === 'sqlsrv';

        if ($isSqlServer) {
            // Step 1: Add new UUID column
            DB::statement('ALTER TABLE wallet_approval_actions ADD new_request_id uniqueidentifier NULL');

            // Step 2: Update the new column with UUIDs from v2_wallet_approval_requests
            DB::statement('
                UPDATE a
                SET a.new_request_id = r.id
                FROM wallet_approval_actions a
                INNER JOIN v2_wallet_approval_requests r ON CAST(a.request_id AS varchar(36)) = CAST(r.id AS varchar(36))
            ');

            // Step 3: Drop the old foreign key and column
            DB::statement('
                DECLARE @constraint_name nvarchar(200)
                SELECT @constraint_name = name
                FROM sys.foreign_keys
                WHERE parent_object_id = OBJECT_ID(\'wallet_approval_actions\')
                AND referenced_object_id = OBJECT_ID(\'v2_wallet_approval_requests\')
                
                IF @constraint_name IS NOT NULL
                    EXEC(\'ALTER TABLE wallet_approval_actions DROP CONSTRAINT \' + @constraint_name)
            ');
            
            DB::statement('ALTER TABLE wallet_approval_actions DROP COLUMN request_id');

            // Step 4: Rename new column to request_id
            DB::statement('EXEC sp_rename \'wallet_approval_actions.new_request_id\', \'request_id\', \'COLUMN\'');

            // Step 5: Make the column not nullable and add foreign key constraint
            DB::statement('ALTER TABLE wallet_approval_actions ALTER COLUMN request_id uniqueidentifier NOT NULL');
            DB::statement('
                ALTER TABLE wallet_approval_actions 
                ADD CONSTRAINT FK_wallet_approval_actions_request_id 
                FOREIGN KEY (request_id) 
                REFERENCES v2_wallet_approval_requests(id) 
                ON DELETE CASCADE
            ');
        } else {
            // MySQL implementation
            Schema::table('wallet_approval_actions', function (Blueprint $table) {
                $table->char('new_request_id', 36)->nullable();
            });

            // MySQL syntax for UPDATE with JOIN
            DB::statement('
                UPDATE wallet_approval_actions waa
                INNER JOIN v2_wallet_approval_requests war 
                ON waa.request_id = war.id
                SET waa.new_request_id = war.id
            ');

            Schema::table('wallet_approval_actions', function (Blueprint $table) {
                try {
                    $table->dropForeign(['request_id']);
                } catch (Exception $e) {
                    // Foreign key might not exist
                }
                $table->dropColumn('request_id');
            });

            // MySQL doesn't have sp_rename, use ALTER TABLE
            DB::statement("ALTER TABLE wallet_approval_actions CHANGE new_request_id request_id CHAR(36) NOT NULL");

            Schema::table('wallet_approval_actions', function (Blueprint $table) {
                $table->foreign('request_id')
                      ->references('id')
                      ->on('v2_wallet_approval_requests')
                      ->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $isSqlServer = Config::get('database.default') === 'sqlsrv';

        if ($isSqlServer) {
            // Step 1: Add temporary bigint column
            DB::statement('ALTER TABLE wallet_approval_actions ADD new_request_id bigint NULL');

            // Step 2: Update the new column with original IDs
            // We'll use ROW_NUMBER to generate sequential IDs instead of trying to cast UUID
            DB::statement('
                WITH NumberedRequests AS (
                    SELECT id,
                           ROW_NUMBER() OVER (ORDER BY created_at) as new_id
                    FROM v2_wallet_approval_requests
                )
                UPDATE a
                SET a.new_request_id = nr.new_id
                FROM wallet_approval_actions a
                INNER JOIN NumberedRequests nr ON a.request_id = nr.id
            ');

            // Step 3: Drop the old foreign key and column
            DB::statement('
                DECLARE @constraint_name nvarchar(200)
                SELECT @constraint_name = name
                FROM sys.foreign_keys
                WHERE parent_object_id = OBJECT_ID(\'wallet_approval_actions\')
                AND referenced_object_id = OBJECT_ID(\'v2_wallet_approval_requests\')
                
                IF @constraint_name IS NOT NULL
                    EXEC(\'ALTER TABLE wallet_approval_actions DROP CONSTRAINT \' + @constraint_name)
            ');
            
            DB::statement('ALTER TABLE wallet_approval_actions DROP COLUMN request_id');

            // Step 4: Rename new column to request_id
            DB::statement('EXEC sp_rename \'wallet_approval_actions.new_request_id\', \'request_id\', \'COLUMN\'');

            // Step 5: Make the column not nullable and add foreign key constraint
            DB::statement('ALTER TABLE wallet_approval_actions ALTER COLUMN request_id bigint NOT NULL');
            
            // Also update the referenced table's ID column
            DB::statement('ALTER TABLE v2_wallet_approval_requests ADD new_id bigint NULL');
            
            DB::statement('
                WITH NumberedRequests AS (
                    SELECT id,
                           ROW_NUMBER() OVER (ORDER BY created_at) as new_id
                    FROM v2_wallet_approval_requests
                )
                UPDATE r
                SET r.new_id = nr.new_id
                FROM v2_wallet_approval_requests r
                INNER JOIN NumberedRequests nr ON r.id = nr.id
            ');
            
            DB::statement('ALTER TABLE v2_wallet_approval_requests DROP COLUMN id');
            DB::statement('EXEC sp_rename \'v2_wallet_approval_requests.new_id\', \'id\', \'COLUMN\'');
            DB::statement('ALTER TABLE v2_wallet_approval_requests ALTER COLUMN id bigint NOT NULL');
            DB::statement('ALTER TABLE v2_wallet_approval_requests ADD PRIMARY KEY (id)');
            
            // Finally add the foreign key constraint
            DB::statement('
                ALTER TABLE wallet_approval_actions 
                ADD CONSTRAINT FK_wallet_approval_actions_request_id 
                FOREIGN KEY (request_id) 
                REFERENCES v2_wallet_approval_requests(id)
            ');
        } else {
            // MySQL implementation - this is not easily reversible
            // For fresh migrations, this rollback is not needed
            echo "WARNING: This migration rollback is not fully supported for MySQL.\n";
            echo "This migration was designed for converting existing data.\n";
            echo "For fresh installations, simply run migrate:fresh instead.\n";
        }
    }
};
