<?php

namespace App\Livewire\CourseAnalytics;

use App\Models\Payment;
use App\Models\Course;
use Carbon\Carbon;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class StatementsTab extends Component
{
    public $payments = [];
    public $selectedCourse = null;
    public $selectedDate = null;
    public $courses;
    public $searchCourses = '';

    public function mount()
    {
        // Fetch courses for the authenticated user that are not drafted
        $this->courses = Course::where('user_id', Auth::id()) // Only courses of the authenticated user
            ->where('is_drafted', false) // Only courses that are not drafted
            ->get();
        $this->loadPayments(); // Load payments initially
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['selectedCourse', 'selectedDate', 'searchCourses'])) {
            $this->loadPayments();  // Reload payments when filters are updated
        }
    }

    public function loadPayments()
    {
        // Initialize query to load payments
        $query = Payment::query();

        // Filter by selected course if any
        if ($this->selectedCourse) {
            $query->whereHas('booking', function ($query) {
                $query->where('course_id', $this->selectedCourse);
            });
        }

        // Filter by date if selected
        if ($this->selectedDate) {
            $query->whereDate('created_at', Carbon::parse($this->selectedDate)->format('Y-m-d'));
        }

        // Search by course name if search term is set
        if ($this->searchCourses) {
            $query->whereHas('booking.course', function ($query) {
                $query->where('title', 'like', '%' . $this->searchCourses . '%');
            });
        }

        // Fetch the payments
        $this->payments = $query->get();
    }
    public function fetchPayments()
    {
        $this->dispatch('refreshComponent');
    }

    public function render()
    {
        return view('livewire.course-analytics.statements-tab');
    }
}
