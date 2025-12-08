<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, integer, boolean, json
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Insert default pagination settings
        DB::table('settings')->insert([
            [
                'key' => 'pagination_students_per_page',
                'value' => '15',
                'type' => 'integer',
                'description' => 'Number of students to display per page',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'pagination_instructors_per_page',
                'value' => '15',
                'type' => 'integer',
                'description' => 'Number of instructors to display per page',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'pagination_admins_per_page',
                'value' => '15',
                'type' => 'integer',
                'description' => 'Number of admins to display per page',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
