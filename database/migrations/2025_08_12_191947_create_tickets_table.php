// database/migrations/xxxx_xx_xx_xxxxxx_create_tickets_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            // e.g. PK0000401  => 2 letters + up to 8 digits  (total max 10 chars)
            $table->string('serial', 10)->unique();
            $table->string('image_path')->nullable();
            $table->unsignedInteger('quantity')->default(1); // fixed default = 1
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
