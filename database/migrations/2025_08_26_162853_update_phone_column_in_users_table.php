<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1) Drop any UNIQUE index on `phone` if it exists (works for MySQL/MariaDB)
        $dbName = DB::getDatabaseName();
        $indexes = DB::select("
            SELECT INDEX_NAME
            FROM information_schema.STATISTICS
            WHERE TABLE_SCHEMA = ?
              AND TABLE_NAME   = 'users'
              AND COLUMN_NAME  = 'phone'
              AND NON_UNIQUE   = 0
        ", [$dbName]);

        foreach ($indexes as $idx) {
            $name = $idx->INDEX_NAME;
            // Safety: skip PRIMARY
            if (strtoupper($name) !== 'PRIMARY') {
                DB::statement("ALTER TABLE `users` DROP INDEX `{$name}`");
            }
        }

        // 2) Backfill NULLs so we can set NOT NULL
        DB::table('users')->whereNull('phone')->update(['phone' => '00000000000']);

        // 3) Make the column NOT NULL (no unique)
        // Use raw SQL to avoid doctrine/dbal
        DB::statement("ALTER TABLE `users` MODIFY `phone` VARCHAR(20) NOT NULL");
    }

    public function down(): void
    {
        // Revert: make it NULL-able again
        DB::statement("ALTER TABLE `users` MODIFY `phone` VARCHAR(20) NULL");

        // (Optional) put unique back if you want on rollback:
        // DB::statement("ALTER TABLE `users` ADD UNIQUE `users_phone_unique` (`phone`)");
    }
};
