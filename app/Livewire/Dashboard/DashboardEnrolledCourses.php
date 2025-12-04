<?php

namespace App\Livewire\Dashboard;

use App\Models\Booking;
use App\Models\CourseProgress;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DashboardEnrolledCourses extends Component
{
    public $enrolledCourses = [];
    public $activeCourses = [];
    public $completedCourses = [];

    public function mount()
    {
        $userId = Auth::id();

        $bookedCourses = Booking::where('user_id', $userId)->get();

        foreach ($bookedCourses as $booking) {
            $course = $booking->course;

            if ($course) {
                $progress = CourseProgress::where('user_id', $userId)
                    ->where('course_id', $booking->course_id)
                    ->value('progress') ?? 0;

                $courseData = [
                    'course' => $course,
                    'progress' => $progress
                ];

                if ($progress == 0) {
                    $this->enrolledCourses[] = $courseData;
                } elseif ($progress > 0 && $progress < 100) {
                    $this->activeCourses[] = $courseData;
                } elseif ($progress == 100) {
                    $this->completedCourses[] = $courseData;
                }
            }
        }
    }

    public function render()
    {
        return view('livewire.dashboard.dashboard-enrolled-courses')->layout('components.layouts.dashboard');
    }
}
