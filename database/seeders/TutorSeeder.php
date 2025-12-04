<?php

namespace Database\Seeders;

use App\Models\Tutor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TutorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tutors = [
            [
                'user_id' => 1,
                'specialization' => 'Mathematics, Physics',
                'preferred_teaching_method' => 'Online',
                'is_verified' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // [
            //     'user_id' => 3,
            //     'specialization' => 'Computer Science, Programming',
            //     'preferred_teaching_method' => 'In-Person',
            //     'is_verified' => true,
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
        ];

        foreach ($tutors as $tutor) {
            Tutor::create($tutor);
        }
    }
}
