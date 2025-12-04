<?php

namespace App\Livewire;

use App\Models\Course;
use App\Models\Tutor;
use Livewire\Component;
use Livewire\WithPagination;

class TutorPrfile extends Component
{
    use WithPagination;

    public $tutorId;
    public $tutor;
    public $tutor_name;
    public $tutor_image;
    public $tutor_bio;

    // Reset pagination when a new tutor is loaded
    protected $updatesQueryString = ['page'];

    public function mount($id = null)
    {
        if ($id) {
            $this->tutorId = $id;
            $this->loadtutordata();
        }
    }

    public function loadtutordata()
    {
        $this->tutor = Tutor::where('user_id', $this->tutorId)->with('user')->first();

        $this->tutor_name = $this->tutor->user->first_name . ' ' . $this->tutor->user->last_name;
        $this->tutor_bio = $this->tutor->user->bio;
    }

    public function getTutorCoursesProperty()
    {
        return Course::where('user_id', $this->tutorId)
            ->with('user')
            ->paginate(6); 
    }

    public function render()
    {
        return view('livewire.tutor-prfile', [
            'tutorCourses' => $this->tutorCourses,
        ]);
    }
}
