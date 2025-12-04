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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('course_type', ['classtype', 'recorded']);
            $table->enum('is_paid', ['free', 'paid'])->nullable();
            $table->decimal('price', 8, 2)->nullable();
            $table->string('title')->unique();
            $table->text('description');
            $table->string('thumbnail');
            $table->string('video_path');
            $table->text('learning_outcomes');
            $table->text('target_audience');
            $table->text('requirements');
            $table->boolean('is_published')->default(false);
            $table->boolean('is_drafted')->default(false);
            $table->string('cerficate_template')->nullable();
            $table->boolean('is_certified')->default(false);
            $table->boolean('is_completed')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
