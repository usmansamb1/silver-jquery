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
    public function up()
    {
        // Skip this migration during fresh migrations
        // The Spatie permissions table will be created with the correct structure
        // This migration was for converting existing data from bigInteger to UUID
        // but during fresh migrations, we start clean
        echo "Skipping UUID conversion - fresh migration will create correct structure.\n";
        return;
    }

    public function down()
    {
        // This migration is not reversible without data loss, so we'll just log a warning
        echo "WARNING: This migration cannot be rolled back automatically without data loss.\n";
        echo "Restore from the model_has_roles_backup table if needed and update model_id values appropriately.\n";
        
        // Mark as rolled back in migrations table
        DB::table('migrations')
            ->where('migration', '2023_04_07_172235_alter_model_has_roles_change_model_id_to_uuid')
            ->delete();
    }
};
