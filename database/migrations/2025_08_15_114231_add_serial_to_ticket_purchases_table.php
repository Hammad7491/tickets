<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('ticket_purchases', function (Blueprint $table) {
            // PK + 6 digits (e.g. PK123456). Keep nullable so old rows are OK.
            $table->string('serial', 8)->nullable()->unique()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('ticket_purchases', function (Blueprint $table) {
            $table->dropUnique(['serial']);
            $table->dropColumn('serial');
        });
    }
};
