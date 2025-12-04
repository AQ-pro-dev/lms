<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   
    public function up(): void
    {
       Schema::create('analytics', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->cascadeOnUpdate();
    $table->string('event');  
    $table->string('entity_type');
    $table->unsignedBigInteger('entity_id'); 
    $table->json('data');  
    $table->timestamps();
});

    }

   
    public function down(): void
    {
        Schema::dropIfExists('analytics');
    }
};
