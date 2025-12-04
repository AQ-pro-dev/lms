<?php

namespace App\Livewire\Dashboard;

use App\Models\Category;
use App\Models\Tag;
use App\Models\Tutor;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;

class CreateCourse extends Component
{
    use WithFileUploads, LivewireAlert;

    public $courseId;
    public $courseTitle;
    public $description;
    public $category = [];
    public $tags = [];
    public $tutors = [];
    public $file;
    public $videoFile;
    public $learnDetails;
    public $audienceDetails;
    public $requirements;

    public $courseType;
    public $is_paid;
    public $price;

    public $selectedTutors = [];
    public $tagslist = [];
    public $categorylist = [];
    public $tutorList = [];

    public $existingThumbnail;
    public $existingVideo;

    protected function rules()
    {
        return [
            'courseTitle' => 'required|string|max:35|unique:courses,title,' . $this->courseId,
            'description' => 'required|string',
            'category' => 'array|min:1',
            'category.*' => 'exists:categories,id',
            'tags' => 'required|array|min:1',
            'tags.*' => 'exists:tags,id',
            'file' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:1024',
            'videoFile' => ($this->videoFile instanceof \Illuminate\Http\UploadedFile)
                ? 'nullable|file|mimes:mp4,mov,avi|max:10240'
                : 'nullable|string',
            'learnDetails' => 'required|string',
            'audienceDetails' => 'required|string',
            'requirements' => 'required|string',
            'courseType' => 'required',
            'is_paid' => 'nullable|required_if:courseType,recorded',
            'price' => 'required_if:is_paid,Paid|min:0',
        ];
    }

    public function uploadVideo()
    {
        if ($this->videoFile instanceof \Illuminate\Http\UploadedFile) {
            return $this->videoFile->store('videos', 'public');

        }
        return null;
    }

    public function uploadThumbnail()
    {
        if ($this->file instanceof \Illuminate\Http\UploadedFile) {
            return $this->file->store('uploads', 'public');
        }

        return null;
    }

    public function mount($courseId = null)
    {
        $this->tagslist = Tag::all();
        $this->categorylist = Category::all();
        $this->tutorList = Tutor::with('user')->get();

        if ($courseId) {
            $this->courseId = $courseId;
            $this->loadCourseData();
        }

        $authTutor = Tutor::where('user_id', Auth::id())->first();
        if ($authTutor) {
            $this->tutors = [$authTutor->id];
            $this->selectedTutors = [$authTutor];
        }
    }

    public function loadCourseData()
    {
        $course = Course::with(['categories', 'tags', 'tutors.user'])->findOrFail($this->courseId);

        $this->courseTitle = $course->title;
        $this->description = $course->description;
        $this->category = $course->categories->pluck('id')->toArray();
        $this->tags = $course->tags->pluck('id')->toArray();
        $this->tutors = $course->tutors->pluck('user_id')->toArray();
        $this->file = $course->thumbnail;
        $this->learnDetails = $course->learning_outcomes;
        $this->audienceDetails = $course->target_audience;
        $this->requirements = $course->requirements;
        $this->existingVideo = $course->video_path;
        $this->existingThumbnail = $course->thumbnail;
        $this->selectedTutors = $course->tutors;
        $this->is_paid = $course->is_paid;
        $this->price = $course->price;
        $this->courseType = $course->course_type;
    }

    public function updatedTutors($value)
    {
        $authTutor = Tutor::where('user_id', Auth::id())->first();

        if ($authTutor && !in_array($authTutor->id, $this->tutors)) {
            $this->tutors[] = $authTutor->id;
        }

        $this->selectedTutors = Tutor::with('user')
            ->whereIn('id', array_unique($this->tutors))
            ->get();
    }

    public function submitCourse()
    {
        if ($this->courseId) {
            $this->updateCourse();
        } else {
            $this->createCourse();
        }
        $this->redirect(route('dashboard.mycourses'));
    }

    public function createCourse()
    {
        $this->validate();

        $filePath = $this->uploadThumbnail();
        $videoPath = $this->uploadVideo();

        $role = Auth::user()->role_id;

        // dd($this->tutors);
        $course = Course::create([
            'user_id' => Auth::id(),
            'title' => $this->courseTitle,
            'description' => $this->description,
            'thumbnail' => $filePath,
            'video_path' => $videoPath,
            'learning_outcomes' => $this->learnDetails,
            'requirements' => $this->requirements,
            'target_audience' => $this->audienceDetails,
            'is_drafted' => false,
            'is_published' => ($role == 1) ? true : false,
            'is_paid' => $this->is_paid,
            'price' => $this->price,
            'course_type' => $this->courseType,
            'is_completed' => $this->courseType === 'recorded' ? false : true,
        ]);


        $course->categories()->attach($this->category);
        $course->tags()->attach($this->tags);
        $course->tutors()->attach($this->tutors);


        $this->resetForm();
        $this->alert('success', 'Course created successfully.');
        $this->redirect(route('dashboard.mycourses'));
    }

    public function updateCourse()
    {
        $this->validate();

        $course = Course::findOrFail($this->courseId);

        $filePath = $this->file instanceof \Illuminate\Http\UploadedFile
            ? $this->uploadThumbnail()
            : $this->existingThumbnail;

        $videoPath = $this->videoFile instanceof \Illuminate\Http\UploadedFile
            ? $this->uploadVideo()
            : $this->videoFile;

        $course->categories()->sync($this->category);
        $course->tags()->sync($this->tags);
        $course->tutors()->sync($this->tutors);

        $this->videoFile = $videoPath;

        $course->categories()->sync($this->category);
        $course->tags()->sync($this->tags);
        $course->tutors()->sync($this->tutors);
    }

    public function saveAsDraft()
    {
        $this->validate();

        $filePath = $this->uploadThumbnail();
        $videoPath = $this->uploadVideo();

        $role = Auth::user()->role_id;

        $course = Course::create([
            'user_id' => Auth::id(),
            'title' => $this->courseTitle,
            'description' => $this->description,
            'thumbnail' => $filePath,
            'video_path' => $this->videoFile ? $videoPath : null,
            'learning_outcomes' => $this->learnDetails,
            'requirements' => $this->requirements,
            'target_audience' => $this->audienceDetails,
            'is_drafted' => true,
            'is_published' => ($role == 1) ? true : false,
            'is_paid' => $this->is_paid,
            'price' => $this->price,
            'course_type' => $this->courseType,
            'is_completed' => $this->courseType === 'recorded' ? false : true,
        ]);

        $course->categories()->attach($this->category);
        $course->tags()->attach($this->tags);
        $course->tutors()->attach($this->tutors);

        $this->resetForm();
        $this->alert('success', 'Course saved as draft.');
        $this->redirect(route('dashboard.mycourses'));
    }

    public function resetForm()
    {
        $this->reset();
        $this->dispatch('clearSelect2');
        $this->reset('selectedTutors');
    }

    public function updateCourseType()
    {
        $this->dispatch('refreshComponent');
    }

    public function updateIsPaid()
    {
        $this->dispatch('refreshComponent');
    }

    public function previewCourse()
    {
        $this->validate();

        // Store course data in the session for preview
        session()->put('preview_course', [
            'courseTitle' => $this->courseTitle,
            'description' => $this->description,
            // 'category' => $this->categorylist->whereIn('id', $this->category)->pluck('name')->toArray(),
            // 'tags' => $this->tagslist->whereIn('id', $this->tags)->pluck('name')->toArray(),
            // 'tutors' => $this->selectedTutors->pluck('user.name')->toArray(),
            'learnDetails' => $this->learnDetails,
            'audienceDetails' => $this->audienceDetails,
            'requirements' => $this->requirements,
            'is_paid' => $this->is_paid,
            'price' => $this->price,
            'courseType' => $this->courseType,
            'thumbnail' => $this->file ? $this->uploadThumbnail() : $this->existingThumbnail,
            'video' => $this->videoFile ? $this->uploadVideo() : $this->videoFile,
        ]);

        // Redirect to the preview page
        return redirect()->route('dashboard.course.preview');
    }

    public function render()
    {
        return view('livewire.dashboard.create-course')->layout('components.layouts.createCourse');
    }

}
