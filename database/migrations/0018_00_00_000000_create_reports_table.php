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
        Schema::create('reports', function (Blueprint $table) {
             $table->id();
             $table->foreignId('class_id')->constrained()->onDelete('cascade')->cascadeOnUpdate();
             $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->cascadeOnUpdate();
             $table->boolean('attended')->default(false);  
             $table->timestamp('join_time')->nullable();  // virtual team classes
             $table->timestamp('leave_time')->nullable(); // virtual team classes
             $table->float('watch_time')->nullable();  // recored lectures
             $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
