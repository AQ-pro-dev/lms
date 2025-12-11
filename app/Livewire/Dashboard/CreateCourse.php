<?php

namespace App\Livewire\Dashboard;

use App\Models\Category;
use App\Models\Tag;
use App\Models\Tutor;
use App\Models\Course;
use App\Models\User;
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
            'file' => ($this->file instanceof \Illuminate\Http\UploadedFile)
                ? 'nullable|image|mimes:jpeg,jpg,png,webp|max:1024'
                : 'nullable|string',
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

    protected function messages()
    {
        return [
            'file.image' => 'The course thumbnail must be an image file.',
            'file.mimes' => 'The course thumbnail must be a file of type: jpeg, jpg, png, or webp.',
            'file.max' => 'The course thumbnail must not be larger than 1MB.',
        ];
    }

    public function uploadVideo()
    {
        if ($this->videoFile instanceof \Illuminate\Http\UploadedFile) {
            return $this->videoFile->store('videos', 'public');
        }
        return ''; // Return empty string to avoid null constraint violation
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
        
        // Get instructors from users table (role_id = 2) and ensure they have Tutor records
        $instructorUsers = User::where('role_id', 2)->get();
        $tutorList = [];
        
        foreach ($instructorUsers as $user) {
            // Get or create Tutor record for this instructor
            $tutor = Tutor::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'specialization' => $user->bio ?? 'General',
                    'preferred_teaching_method' => 'online',
                    'is_verified' => true
                ]
            );
            $tutorList[] = $tutor;
        }
        
        $this->tutorList = collect($tutorList);

        if ($courseId) {
            $this->courseId = $courseId;
            $this->loadCourseData();
        }

        $authTutor = Tutor::where('user_id', Auth::id())->first();
        if ($authTutor) {
            $this->tutors = [$authTutor->user_id];
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
        $this->selectedTutors = $course->tutors;
        $this->file = null; // Don't set file to existing thumbnail - only set when new file is uploaded
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

        if ($authTutor && !in_array($authTutor->user_id, $this->tutors)) {
            $this->tutors[] = $authTutor->user_id;
        }

        // Get tutors by user_id (since tutors array contains user_ids)
        $this->selectedTutors = Tutor::with('user')
            ->whereIn('user_id', array_unique($this->tutors))
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
        try {
            $this->validate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->alert('error', 'Please fix the validation errors before submitting.');
            return;
        }

        $filePath = $this->uploadThumbnail();
        $videoPath = $this->uploadVideo(); // Can be null now

        $role = Auth::user()->role_id;

        // Determine owner: if admin creating and an instructor is selected, assign to that instructor
        $ownerId = Auth::id();
        if ($role == 1 && !empty($this->tutors)) {
            $primaryInstructorId = $this->tutors[0];
            if (User::find($primaryInstructorId)) {
                $ownerId = $primaryInstructorId;
            }
        }

        // Normalize is_paid value to match enum ('free' or 'paid')
        $isPaid = null;
        if ($this->is_paid) {
            $isPaid = strtolower($this->is_paid) === 'paid' ? 'paid' : 'free';
        }

        // For admin-created courses, keep unpublished so they appear in CoursesRequest
        $isPublished = ($role == 1) ? false : false;

        $course = Course::create([
            'user_id' => $ownerId,
            'title' => $this->courseTitle,
            'description' => $this->description,
            'thumbnail' => $filePath,
            'video_path' => $videoPath ?: '', // Use empty string if null to avoid constraint violation
            'learning_outcomes' => $this->learnDetails,
            'requirements' => $this->requirements,
            'target_audience' => $this->audienceDetails,
            'is_drafted' => false,
            'is_published' => $isPublished,
            'is_paid' => $isPaid,
            'price' => $this->price ?: null,
            'course_type' => $this->courseType,
            'is_completed' => $this->courseType === 'recorded' ? false : true,
        ]);


        $course->categories()->attach($this->category);
        $course->tags()->attach($this->tags);
        // Attach tutors - ensure tutor records exist and users exist
        if (!empty($this->tutors)) {
            $tutorIds = [];
            foreach ($this->tutors as $userId) {
                // Verify user exists
                $user = User::find($userId);
                if (!$user) {
                    continue; // Skip if user doesn't exist
                }
                
                // Ensure tutor record exists for this user
                $tutor = Tutor::firstOrCreate(
                    ['user_id' => $userId],
                    [
                        'specialization' => 'General',
                        'preferred_teaching_method' => 'online',
                        'is_verified' => true
                    ]
                );
                $tutorIds[] = $tutor->id;
            }
            if (!empty($tutorIds)) {
                $course->tutors()->attach($tutorIds);
            }
        }


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
            : ($this->existingVideo ?: '');

        // Normalize is_paid value to match enum ('free' or 'paid')
        $isPaid = null;
        if ($this->is_paid) {
            $isPaid = strtolower($this->is_paid) === 'paid' ? 'paid' : 'free';
        }

        $course->update([
            'title' => $this->courseTitle,
            'description' => $this->description,
            'thumbnail' => $filePath,
            'video_path' => $videoPath ?: '', // Use empty string if null to avoid constraint violation
            'learning_outcomes' => $this->learnDetails,
            'requirements' => $this->requirements,
            'target_audience' => $this->audienceDetails,
            'is_paid' => $isPaid,
            'price' => $this->price ?: null,
            'course_type' => $this->courseType,
            'is_completed' => $this->courseType === 'recorded' ? false : true,
        ]);

        $course->categories()->sync($this->category);
        $course->tags()->sync($this->tags);
        // Sync tutors - ensure tutor records exist and users exist
        if (!empty($this->tutors)) {
            $tutorIds = [];
            foreach ($this->tutors as $userId) {
                // Verify user exists
                $user = User::find($userId);
                if (!$user) {
                    continue; // Skip if user doesn't exist
                }
                
                // Ensure tutor record exists for this user
                $tutor = Tutor::firstOrCreate(
                    ['user_id' => $userId],
                    [
                        'specialization' => 'General',
                        'preferred_teaching_method' => 'online',
                        'is_verified' => true
                    ]
                );
                $tutorIds[] = $tutor->id;
            }
            if (!empty($tutorIds)) {
                $course->tutors()->sync($tutorIds);
            }
        }

        $this->videoFile = $videoPath;
    }

    public function saveAsDraft()
    {
        $this->validate();

        $filePath = $this->uploadThumbnail();
        $videoPath = $this->uploadVideo(); // Can be null now

        $role = Auth::user()->role_id;

        // Determine owner: if admin creating and an instructor is selected, assign to that instructor
        $ownerId = Auth::id();
        if ($role == 1 && !empty($this->tutors)) {
            $primaryInstructorId = $this->tutors[0];
            if (User::find($primaryInstructorId)) {
                $ownerId = $primaryInstructorId;
            }
        }

        // Normalize is_paid value to match enum ('free' or 'paid')
        $isPaid = null;
        if ($this->is_paid) {
            $isPaid = strtolower($this->is_paid) === 'paid' ? 'paid' : 'free';
        }

        $course = Course::create([
            'user_id' => $ownerId,
            'title' => $this->courseTitle,
            'description' => $this->description,
            'thumbnail' => $filePath,
            'video_path' => $videoPath ?: '', // Use empty string if null to avoid constraint violation
            'learning_outcomes' => $this->learnDetails,
            'requirements' => $this->requirements,
            'target_audience' => $this->audienceDetails,
            'is_drafted' => true,
            'is_published' => ($role == 1) ? true : false,
            'is_paid' => $isPaid,
            'price' => $this->price ?: null,
            'course_type' => $this->courseType,
            'is_completed' => $this->courseType === 'recorded' ? false : true,
        ]);

        $course->categories()->attach($this->category);
        $course->tags()->attach($this->tags);
        // Attach tutors - ensure tutor records exist and users exist
        if (!empty($this->tutors)) {
            $tutorIds = [];
            foreach ($this->tutors as $userId) {
                // Verify user exists
                $user = User::find($userId);
                if (!$user) {
                    continue; // Skip if user doesn't exist
                }
                
                // Ensure tutor record exists for this user
                $tutor = Tutor::firstOrCreate(
                    ['user_id' => $userId],
                    [
                        'specialization' => 'General',
                        'preferred_teaching_method' => 'online',
                        'is_verified' => true
                    ]
                );
                $tutorIds[] = $tutor->id;
            }
            if (!empty($tutorIds)) {
                $course->tutors()->attach($tutorIds);
            }
        }

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
