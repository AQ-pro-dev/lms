<?php

namespace App\Livewire\Dashboard;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DashboardProfile extends Component
{
    public $user;
    public $skill;

    public function mount()
    {
        $userId = Auth::id();
        $this->user = User::findOrFail($userId);
        $this->skill = optional($this->user->tutor)->specialization ?? 'Not specified';
    }
    public function render()
    {
        return view('livewire.dashboard.dashboard-profile')->layout('components.layouts.dashboard');
    }
}
