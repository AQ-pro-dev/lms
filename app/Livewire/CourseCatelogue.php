<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Course;
use App\Models\Tutor;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class CourseCatelogue extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedCategories = [];
    public $featuredTutors;
    public $categories;

    public function mount()
    {
        $this->selectedCategories = [];
        $this->categories = Category::all();
        $this->featuredTutors = Tutor::with('user')->get();
    }
    public function selectedCategorieschanged($id)
    {
        if (in_array($id, $this->selectedCategories)) {
            $key = array_search($id, $this->selectedCategories);
            unset($this->selectedCategories[$key]);
        } else {
            $this->selectedCategories[] = $id;
        }
        $this->dispatch('refreshComponent');
    }
    public function updatingSearch()
    {
        $this->resetPage();
    }


    public function clearFilters()
    {
        $this->search = '';
        $this->selectedCategories = [];
        $this->resetPage();
        $this->dispatch('clearCategoryFilters');
    }
    public function toggleWishlist($courseId)
    {
        $user = Auth::user();

        if (!$user) {
            session()->flash('error', 'You need to log in to manage your wishlist.');
            return;
        }

        // Toggle wishlist status
        if ($user->wishlist()->where('course_id', $courseId)->exists()) {
            $user->wishlist()->detach($courseId); // Remove from wishlist
        } else {
            $user->wishlist()->attach($courseId); // Add to wishlist
        }

        // Reload the most popular courses to reflect changes
        // $this->loadMostPopularCourses();
    }

    public function render()
    {
        $courses = Course::with(['user', 'categories', 'reviews'])
            ->where('is_published', true)
            ->where('is_drafted', false)
            ->when($this->search, function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%');
            })
            ->when($this->selectedCategories, function ($query) {
                $query->whereHas('categories', function ($categoryQuery) {
                    $categoryQuery->whereIn('categories.id', $this->selectedCategories);
                });
            })
            ->get()
            ->map(function ($course) {
                $course->average_rating = round($course->reviews->avg('rating') ?? 0, 1); // Calculate average rating
                return $course;
            });



        return view('livewire.course-catelogue', [
            'courses' => $courses,
            'featuredTutors' => $this->featuredTutors,
            'categories' => $this->categories,
        ]);
    }
}
