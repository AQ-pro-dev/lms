<?php

namespace App\Livewire\Dashboard;

use App\Jobs\FetchVimeoVideoDetailsJob;
use App\Models\Course;
use App\Models\Lecture;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Vimeo\Laravel\Facades\Vimeo;
use Livewire\Component;
use Livewire\WithFileUploads;

class ViewLectures extends Component
{
    use LivewireAlert;
    use WithFileUploads;

    public $courseId;
    public $course;
    public $selectedLectureId;
    public $lectureTitle;
    public $lectureDescription;
    public $newVideo;
    public $loading = false;

    public function mount($courseId)
    {
        // Initialize the course and lectures
        $this->courseId = $courseId;
        $this->course = Course::with('lectures')->findOrFail($this->courseId);
    }

    public function editLecture($lectureId)
    {
        // Get the selected lecture details
        $lecture = Lecture::findOrFail($lectureId);
        $this->selectedLectureId = $lecture->id;
        $this->lectureTitle = $lecture->title;
        $this->lectureDescription = $lecture->description;
    }

    public function updateLecture()
    {
        $this->loading = true;

        $lecture = Lecture::findOrFail($this->selectedLectureId);
        if ($this->newVideo) {
            $videoFile = $this->uploadToVimeo($this->newVideo);
            $lecture->video_file = $videoFile;
            FetchVimeoVideoDetailsJob::dispatch($lecture->id, $videoFile);
        }
        $lecture->update([
            'title' => $this->lectureTitle,
            'description' => $this->lectureDescription,
        ]);
        $this->loading = false;
        $this->alert('success', 'Lecture updated successfully!');
        $this->dispatch('close-modal');
    }


    // Method to upload a video to Vimeo
    protected function uploadToVimeo($file)
    {
        $response = Vimeo::upload($file->getRealPath(), [
            'name' => $file->getClientOriginalName(),
            'description' => 'New video for lecture',
        ]);

        $videoFile = $response;
        return $videoFile;
    }
    public function confirmDelete($lectureId)
    {
        $this->selectedLectureId = $lectureId;
    }
    public function deleteLecture()
    {
        $lecture = Lecture::findOrFail($this->selectedLectureId);

        if ($lecture->video_file) {
            $this->deleteVimeoVideo($lecture->video_file);
        }
        $lecture->forceDelete();
        $this->alert('success', 'Lecture and its video deleted successfully!');
        $this->dispatch('close-delete-modal');
    }

    public function deleteVimeoVideo($videoUri)
    {
        try {
            Vimeo::request($videoUri, [], 'DELETE');
        } catch (\Exception $e) {
            $this->alert('error', 'Failed to delete video from Vimeo: ' . $e->getMessage());
        }
    }


    public function render()
    {
        // Return the view and pass the lectures to it
        return view('livewire.dashboard.view-lectures', [
            'lectures' => $this->course->lectures,
        ]);
    }
}
