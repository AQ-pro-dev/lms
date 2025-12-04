<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email', 100)->unique();
            $table->string('microsoft_id', 255)->nullable();
            $table->boolean('microsoft_account')->default(false);
            $table->text('refresh_token')->nullable();
            $table->timestamp('token_expires_at')->nullable();
            $table->string('timezone')->default('UTC');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('username')->nullable();
            $table->string('phone')->unique()->nullable();
            $table->text('bio')->nullable();
            $table->string('password');
            $table->string('pfp')->nullable();
            $table->string('cover_photo')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->integer('role_id')->default(3);
            $table->string('verification_token', 100)->nullable();
            $table->string('password_reset_token', 100)->nullable();
            $table->timestamp('password_token_created_at')->nullable();
            $table->string('facebook')->nullable();
            $table->string('twitter')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('website')->nullable();
            $table->string('github')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
