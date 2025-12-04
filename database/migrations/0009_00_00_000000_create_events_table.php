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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->cascadeOnUpdate();
            $table->string('microsoft_event_id')->nullable();
            $table->string('title');
            $table->text('description');
            $table->integer('capacity');
            $table->enum('class_type', ['onsite', 'virtual']);
            $table->string('teams_link')->nullable();
            $table->string('onsite_address')->nullable();
            $table->enum('is_paid', ['free', 'paid'])->default('free');
            $table->decimal('price', 8, 2)->nullable();
            $table->time('class_time');
            $table->date('class_date');
            $table->date('booking_start_date')->nullable();
            $table->date('booking_end_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
