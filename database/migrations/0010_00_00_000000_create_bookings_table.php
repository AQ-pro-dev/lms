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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->enum('payment_status', ['free', 'paid', 'pending', 'refunded', 'unpaid'])->default('free');
            $table->string('order_number')->unique();
            $table->foreignId('class_id')->nullable()->constrained('classes')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('event_id')->nullable()->constrained('events')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade')->cascadeOnUpdate();
            $table->foreignId('course_id')->nullable()->constrained('courses')->onDelete('cascade')->cascadeOnUpdate();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->enum('type', ['automated', 'manual'])->default('automated');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
