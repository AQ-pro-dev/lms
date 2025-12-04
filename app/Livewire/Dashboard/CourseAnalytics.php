<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;

class CourseAnalytics extends Component
{
    public $activeTab = 'overview';

    public  function mount()
    {
        $this->dispatch('renderChart');
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        return view('livewire.dashboard.course-analytics')->layout('components.layouts.dashboard');
    }
}
