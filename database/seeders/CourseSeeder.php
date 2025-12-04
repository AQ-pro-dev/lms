<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $courses = [
            [
                'user_id' => 2, // Tutor user ID
                'title' => 'Introduction to Mathematics',
                'description' => 'This course covers the basics of mathematics including algebra, geometry, and calculus.',
                'course_type' => 'classtype',
                'thumbnail' => 'uploads/math_course.jpg',
                'video_url' => 'https://www.example.com/math-intro',
                'learning_outcomes' => 'Understand basic mathematical concepts and problem-solving techniques.',
                'requirements' => 'Basic knowledge of arithmetic.',
                'target_audience' => 'High school students and beginners in mathematics.',
                'is_published' => true, // Published
                'is_drafted' => false,  // Not in draft (since it's published)
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 3, // Tutor user ID
                'title' => 'Basics of Programming with Python',
                'description' => 'Learn the fundamentals of programming using Python.',
                'course_type' => 'classtype',
                'thumbnail' => 'uploads/python_course.jpg',
                'video_url' => 'https://www.example.com/python-basics',
                'learning_outcomes' => 'Write Python scripts and understand basic programming concepts.',
                'requirements' => 'No prior programming knowledge required.',
                'target_audience' => 'Beginners and students interested in programming.',
                'is_published' => true, // Published
                'is_drafted' => false,  // Not in draft (since it's published)
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 2, // Tutor user ID
                'title' => 'Physics for Beginners',
                'description' => 'A comprehensive introduction to physics covering motion, force, and energy.',
                'course_type' => 'classtype',
                'thumbnail' => 'uploads/physics_course.jpg',
                'video_url' => 'https://www.example.com/physics-beginners',
                'learning_outcomes' => 'Grasp fundamental physics concepts and their real-world applications.',
                'requirements' => 'Basic understanding of mathematics.',
                'target_audience' => 'Students new to physics and science enthusiasts.',
                'is_published' => false, // Not Published
                'is_drafted' => true,   // In draft
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 3, // Tutor user ID
                'title' => 'Web Development Fundamentals',
                'description' => 'An introductory course on web development covering HTML, CSS, and JavaScript.',
                'course_type' => 'classtype',
                'thumbnail' => 'uploads/webdev_course.jpg',
                'video_url' => 'https://www.example.com/web-dev-fundamentals',
                'learning_outcomes' => 'Build basic websites using HTML, CSS, and JavaScript.',
                'requirements' => 'Basic computer knowledge.',
                'target_audience' => 'Aspiring web developers and technology enthusiasts.',
                'is_published' => true, // Published
                'is_drafted' => false,  // Not in draft (since it's published)
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 2, // Tutor user ID
                'title' => 'Advanced Calculus',
                'description' => 'An advanced course on calculus including integrals, derivatives, and their applications.',
                'course_type' => 'classtype',
                'thumbnail' => 'uploads/adv_calculus.jpg',
                'video_url' => 'https://www.example.com/adv-calculus',
                'learning_outcomes' => 'Solve complex calculus problems with ease.',
                'requirements' => 'Completion of a basic mathematics course.',
                'target_audience' => 'Undergraduate students and mathematics enthusiasts.',
                'is_published' => false, // Not Published
                'is_drafted' => true,    // In draft
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($courses as $course) {
            Course::create($course);
        }
    }
}
