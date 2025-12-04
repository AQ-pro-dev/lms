<?php

namespace App\Livewire\Dashboard;

use App\Models\Course;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class CoursesRequest extends Component
{
    use LivewireAlert;
    public $courses;

    public function mount()
    {
        $this->loadCourses();
    }

    public function loadCourses()
    {
        $this->courses = Course::where('is_published', false)
            ->where('is_drafted', false)
            ->with('user')
            ->get();
    }

    public function approveCourse($courseId)
    {
        $course = Course::findOrFail($courseId);
        $course->update(['is_published' => true]); // Mark as approved
        // session()->flash('success', 'Course approved successfully.');
        $this->alert('success', 'Course approved successfully.');
        $this->loadCourses();
    }

    public function rejectCourse($courseId)
    {
        $course = Course::findOrFail($courseId);
        $course->update(['is_published' => 3]); // Mark as rejected
        // session()->flash('success', 'Course rejected successfully.');
        $this->alert('success', 'Course rejected successfully.');
        $this->loadCourses();
    }

    public function render()
    {
        return view('livewire.dashboard.courses-request')
            ->layout('components.layouts.dashboard');
    }
}
