<?php

// database/migrations/xxxx_xx_xx_xxxxxx_drop_unique_from_users_phone.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Make sure phone is a string (keeps leading zeroes) and nullable if you want
        Schema::table('users', function (Blueprint $table) {
            // Change column type to string if it isn't already
            // (Only works if current type is compatible; otherwise create a separate migration step)
            $table->string('phone', 30)->nullable()->change();

            // Drop the unique index
            // You can use either the index name or the column array; both work in recent Laravel
            if (Schema::hasColumn('users', 'phone')) {
                $table->dropUnique('users_phone_unique');   // preferred if you know the name
                // $table->dropUnique(['phone']);           // alternative syntax
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Re-add the unique if you ever rollback
            $table->unique('phone', 'users_phone_unique');
        });
    }
};
