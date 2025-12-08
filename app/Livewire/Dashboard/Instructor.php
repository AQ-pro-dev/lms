<?php

namespace App\Livewire\Dashboard;

use App\Models\User;
use App\Models\Setting;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Hash;

class Instructor extends Component
{
    use WithPagination;

    public $instructorsWithCourses = [];
    public $selectedInstructorId;
    public $selectedInstructorData = [];
    public $selectedInstructorCourses = [];
    public $newPassword = '';
    public $originalInstructorData = [];
    public $hasChanges = false;
    
    // Search property
    public $search = '';
    
    // Create new user properties
    public $showCreateModal = false;
    public $newUser = [
        'first_name' => '',
        'last_name' => '',
        'email' => '',
        'phone' => '',
        'password' => '',
        'password_confirmation' => '',
    ];

    protected $paginationTheme = 'bootstrap';

    public function mount()
    {
        // No need to load data here, it's handled in render
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    protected function getInstructorsProperty()
    {
        $perPage = (int) Setting::get('pagination_instructors_per_page', 15);
        
        $query = User::withTrashed()
            ->where('role_id', 2)
            ->with('courses');

        // Apply search filter
        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('first_name', 'like', '%' . $this->search . '%')
                  ->orWhere('last_name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%')
                  ->orWhere('phone', 'like', '%' . $this->search . '%');
            });
        }

        return $query->paginate($perPage);
    }

    public function blockUser($userId)
    {
        $user = User::withTrashed()->find($userId);

        if (!$user) {
            session()->flash('error', 'User not found.');
            return;
        }

        if (is_null($user->deleted_at)) {
            $user->delete(); // soft delete
            session()->flash('message', 'User account has been blocked successfully.');
        } else {
            session()->flash('warning', 'User is already blocked.');
        }

        // Data will reload automatically via render
    }

    public function unblockUser($userId)
    {
        $user = User::withTrashed()->find($userId);

        if (!$user) {
            session()->flash('error', 'User not found.');
            return;
        }

        if ($user->trashed()) {
            $user->restore();
            session()->flash('message', 'User account has been unblocked successfully.');
        } else {
            session()->flash('warning', 'User is already active.');
        }

        // Data will reload automatically via render
    }

    public function promoteUser($userId, $newRoleId)
    {
        $user = User::withTrashed()->find($userId);

        if ($user) {
            $user->role_id = $newRoleId;
            $user->save();
            session()->flash('message', 'User role has been updated successfully.');
        }

        // Data will reload automatically via render
    }

    public function toggleEmailVerification($userId)
    {
        $user = User::withTrashed()->find($userId);

        if ($user) {
            if ($user->hasVerifiedEmail()) {
                // Deactivate: clear email verification
                $user->email_verified_at = null;
                session()->flash('message', 'User email verification has been deactivated.');
            } else {
                // Activate: set email verification
                $user->email_verified_at = now();
                session()->flash('message', 'User email verification has been activated.');
            }
            $user->save();
        }

        // Data will reload automatically via render
    }

    public function showDetails($userId)
    {
        $user = User::withTrashed()->with('courses')->findOrFail($userId);

        $this->selectedInstructorId = $user->id;
        $this->selectedInstructorData = [
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

        $this->selectedInstructorCourses = $user->courses;
        $this->originalInstructorData = $this->selectedInstructorData;
        $this->newPassword = '';
        $this->hasChanges = false;
    }

    public function updateInstructor()
    {
        $user = User::withTrashed()->findOrFail($this->selectedInstructorId);

        $user->update($this->selectedInstructorData);

        if (!empty($this->newPassword)) {
            $user->password = bcrypt($this->newPassword);
            $user->save();
            $this->newPassword = '';
        }

        session()->flash('message', 'Instructor profile updated successfully.');
        $this->hasChanges = false;
        $this->originalInstructorData = $this->selectedInstructorData;
        $this->dispatch('close-modal');
    }

    public function updated($propertyName)
    {
        $this->hasChanges = $this->selectedInstructorData !== $this->originalInstructorData || !empty($this->newPassword);
    }

    public function openCreateModal()
    {
        $this->showCreateModal = true;
        $this->resetNewUserForm();
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->resetNewUserForm();
    }

    public function resetNewUserForm()
    {
        $this->newUser = [
            'first_name' => '',
            'last_name' => '',
            'email' => '',
            'phone' => '',
            'password' => '',
            'password_confirmation' => '',
        ];
        $this->resetErrorBag();
    }

    public function createUser()
    {
        $this->validate([
            'newUser.first_name' => 'required|string|max:20',
            'newUser.last_name' => 'required|string|max:20',
            'newUser.email' => 'required|email|unique:users,email|max:100',
            'newUser.phone' => 'required|string|max:20|unique:users,phone',
            'newUser.password' => 'required|min:8|confirmed',
        ], [
            'newUser.password.confirmed' => 'The password confirmation does not match.',
        ]);

        try {
            $user = User::create([
                'first_name' => $this->newUser['first_name'],
                'last_name' => $this->newUser['last_name'],
                'email' => $this->newUser['email'],
                'phone' => $this->newUser['phone'],
                'password' => Hash::make($this->newUser['password']),
                'role_id' => 2, // Instructor
                'microsoft_account' => false,
                'timezone' => 'UTC',
                'email_verified_at' => null, // Initially unverified
            ]);

            session()->flash('message', 'Instructor created successfully!');
            $this->closeCreateModal();
            $this->dispatch('closeCreateModal');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to create instructor: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $instructorsPaginated = $this->getInstructorsProperty();
        
        $this->instructorsWithCourses = [];
        foreach ($instructorsPaginated as $instructor) {
            $courses = $instructor->courses;
            $this->instructorsWithCourses[] = [
                'instructor' => $instructor,
                'courses' => $courses,
                'count' => $courses->count(),
            ];
        }
        
        return view('livewire.dashboard.instructor', [
            'instructors' => $instructorsPaginated,
        ])->layout('components.layouts.dashboard');
    }
}
