<?php

namespace App\Livewire\Dashboard;

use App\Jobs\FetchVimeoVideoDetailsJob;
use App\Models\Course;
use App\Models\Lecture as ModelsLecture;
use Vimeo\Laravel\Facades\Vimeo;
use Illuminate\Support\Facades\Storage;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;

class Lecture extends Component
{
    use LivewireAlert, WithFileUploads;

    public $courses = [];
    public $course_id; // Course selected once for all lectures
    public $lectures = [];
    public $alllectures;
    public $totalLectures = 0;
    public $usedOrders = [];

    protected $rules = [
        'course_id' => 'required|exists:courses,id',
        'lectures.*.title' => 'required|string|max:255', // Moved 'title' inside the lectures array
        'lectures.*.description' => 'nullable|string',
        'lectures.*.video_file' => 'required|file|max:102400|mimetypes:video/mp4,video/avi,video/mov',
        'lectures.*.order' => 'required|integer|distinct|min:1',
    ];

    public function mount()
    {
        $this->courses = Course::where('is_published', true)
            ->where('is_drafted', false)
            ->where('course_type', 'recorded')
            ->get();

        // Initialize with one lecture input block
        $this->initializeLectureBlock();
    }
    public function fetchLectures()
    {
        $this->dispatch('refreshComponent');
    }
    public function updatedCourseId()
    {
        if ($this->course_id) {
            $this->alllectures = ModelsLecture::where('course_id', $this->course_id)->get();
            $this->totalLectures = $this->alllectures->count() + count($this->lectures);
            $this->usedOrders = $this->alllectures->pluck('order')->toArray();
        }
    }

    private function initializeLectureBlock()
    {
        $this->lectures[] = [
            'title' => '',
            'description' => '',
            'video_file' => '',
            'order' => '',
        ];
    }

    public function addLectureBlock()
    {
        $this->initializeLectureBlock();
        $this->totalLectures += 1;
        $this->dispatch('refreshComponent');
    }

    public function removeLectureBlock($index)
    {
        unset($this->lectures[$index]);
        $this->lectures = array_values($this->lectures); // Reindex the array
    }

    public function submit()
    {
        $this->validate();

        // Check for duplicate orders in the current lectures
        $orders = array_column($this->lectures, 'order');
        if (count($orders) !== count(array_unique($orders))) {
            $this->alert('error', 'Lecture order must be unique within the same course.');
            return;
        }

        // Proceed with the rest of the logic
        foreach ($this->lectures as $index => $lecture) {
            try {
                // Upload video directly to Vimeo
                $response = Vimeo::upload($lecture['video_file']->getRealPath(), [
                    'name' => $lecture['title'],
                    'description' => $lecture['description'],
                ]);

                // Get the video ID or URL from Vimeo's response
                $videoId = $response;

                // Create lecture record in the database (without duration initially)
                $lectureRecord = ModelsLecture::create([
                    'course_id' => $this->course_id,
                    'title' => $lecture['title'],
                    'description' => $lecture['description'],
                    'video_file' => $videoId, // Vimeo video ID
                    'video_duration' => null, // Initially null
                    'order' => $lecture['order'],
                ]);

                // Dispatch a job to fetch video details later
                FetchVimeoVideoDetailsJob::dispatch($lectureRecord->id, $videoId);
            } catch (\Exception $e) {
                // Handle errors
                $this->alert('error', 'Failed to upload video to Vimeo: ' . $e->getMessage());
                return;
            }
        }

        $this->alert('success', 'Lectures successfully uploaded to Vimeo! Video details will update soon.');
        $this->resetFields();
    }

    private function resetFields()
    {
        $this->course_id = '';
        $this->lectures = [];
        $this->initializeLectureBlock();
        $this->totalLectures = 0;
        $this->usedOrders = [];
    }
    public function updateorder()
    {
        $this->dispatch('refreshComponent');
    }
    public function render()
    {
        return view('livewire.dashboard.lecture')->layout('components.layouts.dashboard');
    }
}
