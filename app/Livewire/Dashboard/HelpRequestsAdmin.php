<?php

namespace App\Livewire\Dashboard;

use App\Models\Notification;
use App\Models\Request;
use App\Mail\HelpRequestResponse;
use App\Mail\HelpRequestRejection;
use Illuminate\Support\Facades\Mail;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class HelpRequestsAdmin extends Component
{
    use LivewireAlert;

    public $helprequests;
    public $selectedRequest;
    public $response;

    protected $rules = [
        'response' => 'required|min:5|max:1000',
    ];

    public function mount()
    {
        $this->loadRequest();
    }

    public function loadRequest()
    {

        Request::whereNull('status')->update(['status' => 'Pending']);

        $this->helprequests = Request::where('status', 'pending')->with('user')->get();
    }

    public function showDetails($requestId)
    {
        $this->selectedRequest = Request::with('user')->findOrFail($requestId);
        $this->response = ''; // Clear previous response
    }

    public function sendResponse()
    {
        $this->validate([
            'response' => 'required|string|max:255',
        ]);

        if ($this->selectedRequest) {
            $user = $this->selectedRequest->user;

            Mail::to($user->email)->send(new HelpRequestResponse($this->response));
            Request::where('id', $this->selectedRequest->id)->update(['status' => 'approved']);

            Notification::create([
                'user_id' => $user->id,
                'message' => '🎉 Your help request has been approved! Check your email for more details.',
                'type' => 'help-request',
            ]);

            $this->loadRequest();
            $this->dispatch('close-modal');

            $this->alert('success', 'Response sent successfully.');
        } else {
            $this->alert('error', 'No request selected.');
        }
    }

    public function rejectRequest()
    {
        // Validate the response field
        $this->validate([
            'response' => 'required|string|max:255',
        ]);

        if ($this->selectedRequest) {
            $user = $this->selectedRequest->user;
            Mail::to($user->email)->send(new HelpRequestRejection($this->response));
            Request::where('id', $this->selectedRequest->id)->update(['status' => 'rejected']);
            Notification::create([
                'user_id' => $user->id,
                'message' => '❌ Your help request has been rejected. Check your email for more details.',
                'type' => 'help-request',
            ]);
            $this->loadRequest();
            $this->dispatch('close-modal');
            $this->alert('success', 'Request rejected successfully.');
        } else {
            $this->alert('error', 'No request selected.');
        }
    }

    public function render()
    {
        return view('livewire.dashboard.help-requests-admin')->layout('components.layouts.dashboard');
    }
}
