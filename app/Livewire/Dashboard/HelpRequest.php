<?php

namespace App\Livewire\Dashboard;

use App\Models\Request;
use Illuminate\Support\Facades\Auth;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;

class HelpRequest extends Component
{
    use WithFileUploads, LivewireAlert;

    public $subject;
    public $request_detail;
    public $request_img;
    public $requests;

    protected $rules = [
        'subject' => 'required|string|max:50',
        'request_detail' => 'required|string',
        'request_img' => 'nullable|image|mimes:jpeg,png,jpg|max:1024',
    ];

    public function mount()
    {
        $this->loadUserRequests();
    }

    public function loadUserRequests()
    {
        $this->requests = Request::where('user_id', Auth::id())->latest()->get();
    }

    public function createRequest()
    {
        $this->validate();

        $path = $this->request_img ? $this->request_img->store('requests', 'public') : null;

        Request::create([
            'user_id' => Auth::id(),
            'subject' => $this->subject,
            'request_detail' => $this->request_detail,
            'request_img' => $path,
        ]);

        $this->alert('success', 'Help Request has been sent.');
        $this->resetForm();
        $this->loadUserRequests(); // Refresh the list of requests
    }

    private function resetForm()
    {
        $this->reset(['subject', 'request_detail', 'request_img']);
    }

    public function render()
    {
        return view('livewire.dashboard.help-request')
            ->layout('components.layouts.dashboard');
    }
}
