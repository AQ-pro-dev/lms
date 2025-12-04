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
        Schema::create('assignment_classes', function (Blueprint $table) {
          $table->id();
          $table->foreignId('assignment_id')->constrained('assignments')->onDelete('cascade')->cascadeOnUpdate();
          $table->foreignId('user_id')->constrained()->onDelete('cascade');
          $table->string('file_path')->nullable();
          $table->enum('grade', ['A', 'B', 'C', 'D', 'F'])->nullable();
          $table->text('feedback')->nullable();
          $table->integer('attempt_number')->default(1);
          $table->boolean('is_graded')->default(false);
          $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignment_classes');
    }
};
