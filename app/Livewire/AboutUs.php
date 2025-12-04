<?php

namespace App\Livewire;

use App\Models\Tutor;
use Livewire\Component;

class AboutUs extends Component
{
    public $tutors;

    public function mount()
    {
        $this->tutors = Tutor::with('user')->get();
        // dd($tutors);
    }
    public function render()
    {
        return view('livewire.about-us');
    }
}
