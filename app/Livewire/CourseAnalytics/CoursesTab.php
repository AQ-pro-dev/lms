<?php

namespace App\Livewire\CourseAnalytics;

use App\Models\Course;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CoursesTab extends Component
{
    public $courses = [];

    public function mount()
    {
        $userId = Auth::id();

        $courses = Course::where('user_id', $userId)
            ->where('is_published', true)
            ->get();

        // Process each course to calculate data
        $this->courses = $courses->map(function ($course) {
            $courseData = [
                'course_name' => $course->title,
                'course_type' => $course->course_type,
                'course_id' => $course->id,
                'total_learners' => 0,
                'total_earnings' => 0,
                'class_count' => 0,
                'lecture_count' => 0,
            ];

            // Count classes or lectures based on course type
            if ($course->course_type == 'class_type') {
                $courseData['class_count'] = $course->classes->count();
            } elseif ($course->course_type == 'recorded') {
                $courseData['lecture_count'] = $course->lectures->count();
            }

            // Count total learners from the bookings table
            $bookings = Booking::where('course_id', $course->id)->get();
            $courseData['total_learners'] = $bookings->count();

            // Sum payments related to those bookings
            $courseData['total_earnings'] = $bookings->sum(function ($booking) {
                return $booking->payments->sum('amount'); // Assuming the payments relation exists
            });

            return $courseData;
        });
    }

    public function render()
    {
        return view('livewire.course-analytics.courses-tab', [
            'courses' => $this->courses,
        ]);
    }
}
