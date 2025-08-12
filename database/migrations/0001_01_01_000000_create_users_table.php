<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->string('name');                        // Not unique
            $table->string('email')->unique();
            $table->string('phone', 20)->nullable()->unique(); // Phone number
            $table->timestamp('email_verified_at')->nullable();

            $table->string('password')->nullable();        // Nullable for social login only accounts

            // Role column (default: user)
            $table->string('role')->default('user')->index();

            // Admin block/unblock feature
            $table->boolean('is_blocked')->default(false)->index();

            // Social login IDs
            $table->string('google_id', 191)->nullable()->unique();
            $table->string('facebook_id', 191)->nullable()->unique();

            // Profile image path or URL
            $table->string('avatar')->nullable();

            $table->rememberToken();
            $table->timestamps();
            // $table->softDeletes(); // Uncomment if you want soft deletes
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
