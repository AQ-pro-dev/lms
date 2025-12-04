<?php

namespace App\Livewire\Dashboard;

use App\Models\User;
use Livewire\Component;

class Student extends Component
{
    public $studentsWithEnrollments = [];
    public $selectedStudentId;
    public $selectedStudentData = [];
    public $selectedStudentCourses = [];
    public $newPassword = '';
    public $originalStudentData = [];
    public $hasChanges = false;

    public function mount()
    {
        $this->loadStudentData();
    }

    public function loadStudentData()
    {
        $students = User::withTrashed()
            ->where('role_id', 3)
            ->with('bookings.course')
            ->get();

        $this->studentsWithEnrollments = [];

        foreach ($students as $student) {
            $enrolledCourses = $student->bookings->pluck('course')->filter();

            $this->studentsWithEnrollments[] = [
                'student' => $student,
                'courses' => $enrolledCourses,
                'count' => $enrolledCourses->count(),
            ];
        }
    }

    public function blockUser($userId)
    {
        $user = User::withTrashed()->find($userId);

        if ($user && is_null($user->deleted_at)) {
            $user->delete(); // soft delete
        }

        $this->loadStudentData();
    }

    public function unblockUser($userId)
    {
        $user = User::withTrashed()->find($userId);

        if ($user && $user->trashed()) {
            $user->restore();
        }

        $this->loadStudentData();
    }

    public function promoteUser($userId, $newRoleId)
    {
        $user = User::withTrashed()->find($userId);

        if ($user) {
            $user->role_id = $newRoleId;
            $user->save();
        }

        $this->loadStudentData();
    }

    public function showDetails($userId)
    {
        $user = User::withTrashed()->with('bookings.course')->findOrFail($userId);

        $this->selectedStudentId = $user->id;
        $this->selectedStudentData = [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'username' => $user->username,
            'email' => $user->email,
            'phone' => $user->phone,
            'bio' => $user->bio,
            'timezone' => $user->timezone,
            'facebook' => $user->facebook,
            'twitter' => $user->twitter,
            'linkedin' => $user->linkedin,
            'website' => $user->website,
            'github' => $user->github,
            'microsoft_account' => $user->microsoft_account,

        ];
        // dd($this->selectedStudentData['microsoft_account']);

        $this->selectedStudentCourses = $user->bookings->pluck('course')->filter();
        $this->originalStudentData = $this->selectedStudentData;
        $this->newPassword = '';
        $this->hasChanges = false;
    }

    public function updateStudent()
    {
        $user = User::withTrashed()->findOrFail($this->selectedStudentId);

        $user->update($this->selectedStudentData);

        if (!empty($this->newPassword)) {
            $user->password = bcrypt($this->newPassword);
            $user->save();
            $this->newPassword = '';
        }

        session()->flash('message', 'Student profile updated successfully.');
        $this->loadStudentData();
        $this->hasChanges = false;
        $this->originalStudentData = $this->selectedStudentData;
        $this->dispatch('close-modal'); // Close modal after deletion
    }

    public function updated($propertyName)
    {
        $this->hasChanges = $this->selectedStudentData !== $this->originalStudentData || !empty($this->newPassword);
    }

    public function render()
    {
        return view('livewire.dashboard.student')->layout('components.layouts.dashboard');
    }
}
