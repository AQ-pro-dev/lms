<?php

namespace App\Livewire\Dashboard;

use App\Models\Course;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class MyCourses extends Component
{
    use LivewireAlert;

    public $publishedCourses;
    public $pendingCourses;
    public $draftedCourses;
    public $loggedInUser;
    public $courseId;
    public $activeTab = 'published'; // Default tab

    public function mount()
    {
        $this->loggedInUser = Auth::user();
        $this->refreshCoursedata();
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab; // Change the active tab
    }

    public function refreshCoursedata()
    {
        $user = Auth::user();

        // Check if user is Admin (role_id == 1)
        if ($user->role_id == 1) {
            // Admin sees ALL courses
            $this->publishedCourses = Course::withCount('bookings')
                ->where('is_published', true)
                ->where('is_drafted', false)
                ->get();

            $this->pendingCourses = Course::withCount('bookings')
                ->where('is_published', false)
                ->where('is_drafted', false)
                ->get();

            $this->draftedCourses = Course::withCount('bookings')
                ->where('is_published', false)
                ->where('is_drafted', true)
                ->get();
        } else {
            // Instructor sees ONLY their own courses
            $this->publishedCourses = Course::where('user_id', $user->id) 
                ->where('is_published', true)
                ->where('is_drafted', false)
                ->get();

            $this->pendingCourses = Course::where('user_id', $user->id)
                ->where('is_published', false)
                ->where('is_drafted', false)
                ->get();

            $this->draftedCourses = Course::where('user_id', $user->id)
                ->where('is_published', false)
                ->where('is_drafted', true)
                ->get();
        }
    }

    public function confirmDelete($id)
    {
        $this->courseId = $id;
    }

    public $enrolledStudents = [];
    public $selectedCourseTitle;

    public function destroyCourse()
    {
        Course::findOrFail($this->courseId)->delete();
        $this->alert('success', 'Course deleted successfully.');
        $this->reset(['courseId']);
        $this->dispatch('close-modal'); // Close modal
        $this->refreshCoursedata();
    }

    public function viewStudents($courseId)
    {
        $course = Course::with('bookings.user')->findOrFail($courseId);
        $this->selectedCourseTitle = $course->title;
        
        // Extract unique users from bookings
        $this->enrolledStudents = $course->bookings->map(function ($booking) {
            return $booking->user;
        })->unique('id')->values();
    }

    public function render()
    {
        return view('livewire.dashboard.my-courses', [
            'publishedCourses' => $this->publishedCourses,
            'pendingCourses' => $this->pendingCourses,
            'draftedCourses' => $this->draftedCourses,
            'activeTab' => $this->activeTab,
        ])->layout('components.layouts.dashboard');
    }
}
