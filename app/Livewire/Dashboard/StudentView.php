<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;

class StudentView extends Component
{
    public $studentdata;
    public function mount($id)
    {
        // dd($id);
        $this->loadStudentData();
    }
    public function loadStudentData() {}
    public function render()
    {
        return view('livewire.dashboard.student-view')->layout('components.layouts.dashboard');
    }
}
