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
            $table->string('code', 4)->unique();            // unique 4-digit
            $table->decimal('price', 10, 2)->default(0);     // PKR
            $table->unsignedTinyInteger('quantity')->default(1); // fixed 1
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
