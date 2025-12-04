<?php

namespace App\Livewire\Dashboard;

use App\Models\Announcement;
use App\Models\Course;
use App\Models\Booking;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class Announcements extends Component
{
    use LivewireAlert;
    public $courses;
    public $selectedCourse;
    public $subject;
    public $message;
    public $courseFilter;
    public $sortOrderFilter;
    public $dateFilter;

    protected $rules = [
        'selectedCourse' => 'required|exists:courses,id',
        'subject' => 'required|string|min:5|max:20',
        'message' => 'required|string|min:5|max:50',
    ];

    public function mount()
    {
        // Fetch courses associated with the logged-in user
        $this->courses = Course::where('user_id', Auth::id())
            ->where('is_published', true)
            ->get();
    }

    public function createAnnouncement()
    {
        $this->validate();

        // Save the announcement
        try {
            $announcement = Announcement::create([
                'user_id' => Auth::id(),
                'course_id' => $this->selectedCourse,
                'subject' => $this->subject,
                'message' => $this->message,
            ]);

            // Send emails to enrolled students
            $students = Booking::where('course_id', $this->selectedCourse)->pluck('user_id');
            $emails = User::whereIn('id', $students)->pluck('email')->toArray();

            if (count($emails) > 0) {
                $this->sendEmails($emails); // Use a separate method to send emails
            }

            // Add notifications for all enrolled students
            foreach ($students as $studentId) {
                \App\Models\Notification::create([
                    'user_id' => $studentId,
                    'message' => $this->subject,
                    'type' => 'general',
                    'read_status' => 0,
                ]);
            }

            // Reset fields and close modal
            $this->reset(['selectedCourse', 'subject', 'message']);
            $this->dispatch('closeModal'); // Close the modal

            // Log success and display flash message
            Log::info('Announcement created successfully', ['announcement_id' => $announcement->id]);
            $this->alert('success', 'Announcement sent successfully!');
        } catch (\Exception $e) {
            Log::error("Failed to create announcement: " . $e->getMessage());
            $this->alert('error', 'Failed to create announcement. Please try again.');
        }
    }
    public function filterAnnouncements()
    {
        $this->dispatch('refreshComponent');
    }

    // Method to handle batch email sending
    private function sendEmails($emails)
    {
        try {
            foreach ($emails as $email) {
                Mail::to($email)->send(new \App\Mail\AnnouncementMail($this->subject, $this->message));
            }
        } catch (\Exception $e) {
            // Log the error for failed email sending
            Log::error("Failed to send email: " . $e->getMessage());
            $this->alert('error', 'Failed to send emails. Please try again.');
        }
    }

    public function render()
    {
        $announcements = Announcement::query();

        // Filter by selected course
        if ($this->courseFilter) {
            $announcements->where('course_id', $this->courseFilter);
        }

        // Filter by sort order
        if ($this->sortOrderFilter) {
            $announcements->orderBy('created_at', $this->sortOrderFilter);
        }

        // Filter by date
        if ($this->dateFilter) {
            $announcements->whereDate('created_at', Carbon::parse($this->dateFilter)->toDateString());
        }

        // Get announcements with filters applied
        $announcements = $announcements->get();

        return view('livewire.dashboard.announcements', [
            'announcements' => $announcements,
        ])->layout('components.layouts.dashboard');
    }
}
