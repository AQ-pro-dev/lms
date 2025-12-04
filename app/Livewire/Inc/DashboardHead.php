<?php

namespace App\Livewire\Inc;

use App\Models\Notification;
use App\Models\Tutor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithFileUploads;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class DashboardHead extends Component
{
    use WithFileUploads, LivewireAlert;

    public $username, $specialization, $preferred_teaching_method, $experience, $file_path;
    public $notifications, $unreadCount;

    protected $listeners = ['refreshNotifications' => 'loadnotifications', 'resetForm'];

    public function mount()
    {
        $user = Auth::user();
        $this->username = $user->first_name . ' ' . $user->last_name;
        $this->loadnotifications();
    }

    public function createTutor()
    {
        $this->validate([
            'specialization' => 'required|string|max:50',
            'preferred_teaching_method' => 'required|in:online,in-person',
            'experience' => 'required|string|max:1000',
            'file_path' => 'required|file|mimes:pdf,doc,docx,txt|max:2048',
        ]);

        try {
            DB::beginTransaction();

            $user = Auth::user();

            if (Tutor::where('user_id', $user->id)->exists()) {
                $this->alert('error', 'You have already submitted a tutor request.');
                return;
            }

            // $user->roles()->sync([2]); // Assign tutor role
            $tutor = Tutor::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'specialization' => $this->specialization,
                    'preferred_teaching_method' => $this->preferred_teaching_method,
                    'is_verified' => false,
                ]
            );

            $filePath = $this->file_path->store('tutor_files');
            $tutor->tutorFiles()->create(['file_path' => $filePath]);

            $this->dispatch('close-modal');
            $this->alert('success', 'Tutor profile created successfully.');
            DB::commit();
            $this->resetForm();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Tutor Creation Failed', ['error' => $e->getMessage()]);
            $this->alert('error', 'Something went wrong. Please try again.');
        }
    }

    private function resetForm()
    {
        $this->reset(['specialization', 'preferred_teaching_method', 'experience', 'file_path']);
    }

    public function loadnotifications()
    {
        $user = Auth::user();
        $this->notifications = Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->where('read_status', false)
            ->limit(10)
            ->get();
        $this->unreadCount = Notification::where('user_id', $user->id)
            ->where('read_status', false)
            ->count();
    }

    public function markAsRead($id)
    {
        $notification = Notification::find($id);
        if ($notification && !$notification->read_status) {
            $notification->update(['read_status' => true]);
            $this->loadnotifications();
            // $this->alert('success', 'Notification marked as read.');
        }
    }

    public function render()
    {
        return view('livewire.inc.dashboard-head');
    }
}
