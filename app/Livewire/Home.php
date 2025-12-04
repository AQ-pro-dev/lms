<?php

namespace App\Livewire;

use App\Models\Course;
use App\Models\Email;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class Home extends Component
{
    use LivewireAlert;

    public $courses;
    public $email;

    public function mount()
    {
        $this->courses = Course::with(['user', 'tutors'])
            ->where('is_published', true)
            ->where('is_drafted', false)
            ->get();
    }
    public function submitEmail()
    {
        // Validate the email input
        $this->validate([
            'email' => 'required|email|unique:emails,email',
        ]);

        // Save the email to the database
        Email::create([
            'email' => $this->email,
        ]);


        $this->alert('success', 'Thank you for subscribing!');

        // Reset the email input field
        $this->email = '';
    }
    public function render()
    {
        return view('livewire.home');
    }
}
