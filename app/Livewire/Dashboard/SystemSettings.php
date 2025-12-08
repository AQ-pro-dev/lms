<?php

namespace App\Livewire\Dashboard;

use App\Models\Setting;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class SystemSettings extends Component
{
    use LivewireAlert;

    public $studentsPerPage = 15;
    public $instructorsPerPage = 15;
    public $adminsPerPage = 15;

    public function mount()
    {
        $this->loadSettings();
    }

    public function loadSettings()
    {
        $this->studentsPerPage = (int) Setting::get('pagination_students_per_page', 15);
        $this->instructorsPerPage = (int) Setting::get('pagination_instructors_per_page', 15);
        $this->adminsPerPage = (int) Setting::get('pagination_admins_per_page', 15);
    }

    public function saveSettings()
    {
        $this->validate([
            'studentsPerPage' => 'required|integer|min:5|max:100',
            'instructorsPerPage' => 'required|integer|min:5|max:100',
            'adminsPerPage' => 'required|integer|min:5|max:100',
        ]);

        Setting::set('pagination_students_per_page', $this->studentsPerPage, 'integer', 'Number of students to display per page');
        Setting::set('pagination_instructors_per_page', $this->instructorsPerPage, 'integer', 'Number of instructors to display per page');
        Setting::set('pagination_admins_per_page', $this->adminsPerPage, 'integer', 'Number of admins to display per page');

        $this->alert('success', 'Settings saved successfully!');
    }

    public function render()
    {
        return view('livewire.dashboard.system-settings')->layout('components.layouts.dashboard');
    }
}
