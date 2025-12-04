<?php

namespace App\Livewire\CourseAnalytics;

use App\Models\Booking;
use App\Models\Course;
use App\Models\CourseProgress;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Carbon\Carbon;

class StudentsTab extends Component
{
    public $students = [];
    public $search = '';
    public $selectedCourse = null;
    public $selectedDate = null;
    public $courses;

    public function mount()
    {
        // Load available courses for filtering
        $this->courses = Course::where('is_published', true)
            ->where('is_drafted', false)
            ->where('user_id', Auth::id())
            ->get();

        // Initially load the students
        $this->loadStudents();
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['search', 'selectedCourse', 'selectedDate'])) {
            $this->loadStudents();  // Load students whenever a filter changes
        }
    }

    public function loadStudents()
    {
        // Query to fetch student bookings for filtering
        $courses = Course::where('is_published', true)
            ->where('is_drafted', false)
            ->where('user_id', Auth::id())
            ->pluck('id');

        // Start the query for bookings
        $query = Booking::whereIn('course_id', $courses);

        // Apply the search filter if present
        if ($this->search) {
            $query->whereHas('user', function ($query) {
                $query->where('first_name', 'like', '%' . $this->search . '%')
                    ->orWhere('last_name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        // Apply the course filter if selected
        if ($this->selectedCourse) {
            // dd($this->selectedCourse);
            $query->where('course_id', $this->selectedCourse);
        }

        // Apply the date filter if selected
        if ($this->selectedDate) {
            $query->whereDate('created_at', Carbon::parse($this->selectedDate)->format('Y-m-d'));
        }

        // Fetch the filtered bookings
        $bookings = $query->get();

        // Map the bookings to the required student data
        $this->students = $bookings->map(function ($booking) {
            $progress = CourseProgress::where('user_id', $booking->user_id)
                ->where('course_id', $booking->course_id)
                ->first();
            return [
                'user' => $booking->user,
                'course' => $booking->course,
                'registration_date' => $booking->created_at,
                'progress' => $progress ? $progress->progress : 0, // Default progress to 0 if not found
            ];
        });
    }
    public function fetchStudents()
    {
        $this->dispatch('refreshComponent');
    }

    public function render()
    {
        return view('livewire.course-analytics.students-tab');
    }
}
