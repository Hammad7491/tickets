<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('winners', function (Blueprint $table) {
            $table->id();
            $table->string('name', 191);
            $table->string('serial_number', 60)->unique(); // <-- used by your model/controller
            $table->decimal('price', 10, 2)->default(0);   // cast as decimal:2 in model
            $table->timestamps();

            // Optional helpful indexes (uncomment if needed)
            // $table->index('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('winners');
    }
};
