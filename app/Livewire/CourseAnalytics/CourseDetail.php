<?php

namespace App\Livewire\CourseAnalytics;

use Livewire\Component;

class CourseDetail extends Component
{
    public function render()
    {
        return view('livewire.course-analytics.course-detail')->layout('components.layouts.dashboard');
    }
}
