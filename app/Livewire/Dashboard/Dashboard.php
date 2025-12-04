<?php

namespace App\Livewire\Dashboard;

use App\Models\Booking;
use App\Models\Course;
use App\Models\CourseProgress;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Dashboard extends Component
{
    public $studentCount;
    public $totalCourses;
    public $bookedCoursesCount;
    public $inProgressCoursesCount;
    public $completedCoursesCount;
    public $inProgressCourses;
    public $courses;


    public function mount()
    {
        $userId = Auth::id();
        $this->getStudentCount();
        $this->totalCourses = Course::where('user_id', Auth::id())->count();
        $this->bookedCoursesCount = Booking::where('user_id', $userId)->distinct('course_id')->count('course_id');

        $this->inProgressCoursesCount = CourseProgress::where('user_id', $userId)
            ->where('progress', '>', 0)
            ->where('progress', '<', 100)
            ->count();

        $this->completedCoursesCount = CourseProgress::where('user_id', $userId)
            ->where('progress', '=', 100)
            ->count();

        $this->getInProgressCourses();
        $this->getTutorCourses();
    }
    public function getStudentCount()
    {
        $userId = Auth::id();

        // Count students enrolled or booked in courses created by the logged-in user
        $this->studentCount = Booking::whereIn('course_id', function ($query) use ($userId) {
            $query->select('id')
                ->from('courses')
                ->where('user_id', $userId);
        })->count();
    }
    public function getInProgressCourses()
    {
        $userId = Auth::id();

        // Fetch courses in progress (progress > 0 and < 100) with rating data
        $this->inProgressCourses = CourseProgress::where('user_id', $userId)
            ->where('progress', '>', 0)
            ->where('progress', '<', 100)
            ->with(['course' => function ($query) {
                $query->with(['reviews' => function ($q) {
                    $q->select('course_id', DB::raw('AVG(rating) as average_rating'), DB::raw('COUNT(*) as review_count'))
                        ->groupBy('course_id');
                }]);
            }])
            ->get();
    }
    public function getTutorCourses()
    {
        $userId = Auth::id();

        $this->courses = Course::where('user_id', $userId)
            ->withCount(['bookings as enrolled_students' => function ($query) {
                $query->where('payment_status', 'paid'); // Count only paid bookings
            }])
            ->with(['reviews' => function ($query) {
                $query->select('course_id', DB::raw('MAX(rating) as highest_rating'))
                    ->groupBy('course_id');
            }])
            ->get();

        // return $courses;
    }
    public function render()
    {
        return view('livewire.dashboard.dashboard')->layout('components.layouts.dashboard');
    }
}
