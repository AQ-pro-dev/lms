<?php

namespace App\Livewire\Inc;

use App\Models\Category;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Header extends Component
{
    public $categories;
    public $uniqueCourses;

    public function mount()
    {
        // Fetch all categories with their courses
        $categories = Category::with('courses')->get();

        // Collect unique courses (remove duplicates by ID)
        $this->uniqueCourses = $categories->flatMap->courses->unique('id');

        // Retain categories for the Blade structure
        $this->categories = $categories;
    }
    public function logout()
    {
        session()->invalidate();
        session()->regenerateToken();
        Auth::logout();

        return redirect()->route('login');
    }
    public function render()
    {
        return view('livewire.inc.header');
    }
}
