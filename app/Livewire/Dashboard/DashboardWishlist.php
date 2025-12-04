<?php

namespace App\Livewire\Dashboard;

use App\Models\Wishlist;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DashboardWishlist extends Component
{
    public $wishlists = [];

    public function mount()
    {
        $this->getWishlist();
    }

    public function getWishlist()
    {
        $this->wishlists = Wishlist::with('course', 'course.user') // Include course and course's user data
            ->where('user_id', Auth::id()) // Fetch wishlist of the logged-in user
            ->get();
    }

    public function toggleWishlist($courseId)
    {
        $user = Auth::user();

        if (!$user) {
            session()->flash('error', 'You need to log in to manage your wishlist.');
            return;
        }

        // Toggle wishlist status
        $wishlist = Wishlist::where('course_id', $courseId)->where('user_id', $user->id)->first();
        if ($wishlist) {
            $wishlist->delete(); // Remove from wishlist
        } else {
            Wishlist::create(['user_id' => $user->id, 'course_id' => $courseId]); // Add to wishlist
        }

        // Refresh the wishlist data
        $this->getWishlist();
    }

    public function render()
    {
        return view('livewire.dashboard.dashboard-wishlist')
            ->layout('components.layouts.dashboard');
    }
}
