<?php

namespace App\Livewire\CoursesCards;

use App\Models\Tutor;
use Livewire\Component;

class InstructorCards extends Component
{
    public $featuredTutors;
    public function mount (){
        $this->featuredTutors = Tutor::with('user')->get();
    }
    public function render()
    {
        return view('livewire.courses-cards.instructor-cards');
    }
}
