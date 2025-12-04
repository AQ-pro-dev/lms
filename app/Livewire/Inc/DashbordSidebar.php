<?php

namespace App\Livewire\Inc;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DashbordSidebar extends Component
{

    public function logout()
    {
        session()->invalidate();
        session()->regenerateToken();
        Auth::logout();

        return redirect()->route('login');
    }
    public function render()
    {
        return view('livewire.inc.dashbord-sidebar');
    }
}
