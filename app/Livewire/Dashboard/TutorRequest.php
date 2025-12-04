<?php

namespace App\Livewire\Dashboard;

use App\Mail\TutorApprovalMail;
use App\Mail\TutorRejectionMail;
use App\Models\Notification;
use App\Models\Tutor;
use App\Models\TutorFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class TutorRequest extends Component
{
    public $tutors;
    public $rejectionReason = '';
    public $approvalMessage = '';
    public $tutorId = null;

    use LivewireAlert;

    public function mount()
    {
        $this->loadTutors();
    }

    public function loadTutors()
    {
        $this->tutors = Tutor::with(['user', 'tutorFiles'])->get();
    }

    public function openRejectModal($tutorId)
    {
        $this->tutorId = $tutorId;
        $this->dispatch('showRejectModal');
    }

    public function openApproveModal($tutorId)
    {
        $this->tutorId = $tutorId;
        $this->dispatch('showApproveModal');
    }

    public function submitRejection()
    {
        $tutor = Tutor::find($this->tutorId);

        if ($tutor) {
            // Reject the tutor request
            $tutor->update(['is_verified' => 3]);

            // Send rejection email
            Mail::to($tutor->user->email)->send(new TutorRejectionMail($tutor, $this->rejectionReason));

            // Create a rejection notification
            Notification::create([
                'user_id' => $tutor->user_id,
                'message' => 'Your tutor request has been rejected ❌. Please check your email for details.',
                'type' => 'cancellation',
            ]);

            $this->alert('success', 'Tutor rejected successfully and email sent.');

            // Reset modal fields
            $this->rejectionReason = '';
        } else {
            $this->alert('error', 'Tutor not found.');
        }

        // Reload tutors
        $this->loadTutors();
        // Close modal
        $this->dispatch('hideRejectModal');
    }

    public function submitApproval()
    {
        $tutor = Tutor::find($this->tutorId);
        $user = $tutor->user;

        if ($tutor) {
            $tutor->update(['is_verified' => 1]);
            $user->update(['role_id' => '2']);

            Mail::to($tutor->user->email)->send(new TutorApprovalMail($tutor, $this->approvalMessage));

            Notification::create([
                'user_id' => $tutor->user_id,
                'message' => "Congratulations! Your tutor request has been approved 🎉. Please check your email for details.",
                'type' => 'congratulations',
            ]);

            $this->alert('success', 'Tutor approved successfully and email sent.');

            $this->approvalMessage = '';
        } else {
            $this->alert('error', 'Tutor not found.');
        }

        $this->loadTutors();
        $this->dispatch('hideApproveModal');
    }

    public function downloadFile($filePath)
    {
        if (Storage::disk('private')->exists($filePath)) {
            return Storage::disk('private')->download($filePath);
        } else {
            $this->alert('error', 'File not found.');
        }
    }

    public function render()
    {
        return view('livewire.dashboard.tutor-request')
            ->layout('components.layouts.dashboard');
    }
}
