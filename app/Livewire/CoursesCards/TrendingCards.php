<?php

namespace App\Livewire\CoursesCards;

use App\Models\Course;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TrendingCards extends Component
{
    public $trendingCourses;
    public function mount()
    {
        $this->loadTrendingCourses();
    }
    public function loadTrendingCourses()
    {
        $this->trendingCourses = Course::with(['user', 'reviews'])
            ->where('is_published', true)
            ->where('is_drafted', false)
            ->get()
            ->map(function ($course) {
                $course->average_rating = round($course->reviews->avg('rating') ?? 0, 1); // Rounded to 1 decimal
                return $course;
            });
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
        $this->loadNewCourses();
    }
    public function render()
    {
        return view('livewire.courses-cards.trending-cards');
    }
}
