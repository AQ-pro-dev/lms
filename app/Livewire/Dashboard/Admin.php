<?php

namespace App\Livewire\Dashboard;

use App\Models\User;
use App\Models\Setting;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Hash;

class Admin extends Component
{
    use WithPagination;

    public $selectedAdminId;
    public $selectedAdminData = [];
    public $newPassword = '';
    public $originalAdminData = [];
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

    protected function getAdminsProperty()
    {
        $perPage = (int) Setting::get('pagination_admins_per_page', 15);
        
        $query = User::withTrashed()
            ->where('role_id', 1);

        // Apply search filter
        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('first_name', 'like', '%' . $this->search . '%')
                  ->orWhere('last_name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%')
                  ->orWhere('phone', 'like', '%' . $this->search . '%');
            });
        }

        $admins = $query->paginate($perPage);
        
        // Ensure super admin (id = 1) is never soft-deleted
        $superAdmin = $admins->firstWhere('id', 1);
        if ($superAdmin && $superAdmin->trashed()) {
            $superAdmin->restore();
        }
        
        return $admins;
    }
    
    public function getAdminsArrayProperty()
    {
        return $this->admins->items();
    }

    public function blockUser($userId)
    {
        // Prevent blocking super admin (id = 1)
        if ($userId == 1) {
            session()->flash('error', 'Cannot block super admin account.');
            return;
        }

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
        // Prevent unblocking super admin (id = 1) - though super admin should never be blocked
        if ($userId == 1) {
            session()->flash('error', 'Super admin account cannot be modified.');
            return;
        }

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
        // Prevent promoting/demoting super admin (id = 1)
        if ($userId == 1) {
            session()->flash('error', 'Cannot change super admin role.');
            return;
        }

        $user = User::withTrashed()->find($userId);

        if ($user) {
            $oldRoleId = $user->role_id;
            $user->role_id = $newRoleId;
            $user->save();
            
            // Determine where the user will appear now
            $roleNames = [1 => 'Admin', 2 => 'Instructor', 3 => 'Student'];
            $newRoleName = $roleNames[$newRoleId] ?? 'User';
            $oldRoleName = $roleNames[$oldRoleId] ?? 'User';
            
            if ($newRoleId == 2) {
                session()->flash('message', "User has been demoted from {$oldRoleName} to {$newRoleName}. They will now appear in the Instructors section.");
            } elseif ($newRoleId == 3) {
                session()->flash('message', "User has been demoted from {$oldRoleName} to {$newRoleName}. They will now appear in the Students section.");
            } else {
                session()->flash('message', "User role has been updated from {$oldRoleName} to {$newRoleName}.");
            }
        }

        // Data will reload automatically via render
    }

    public function toggleEmailVerification($userId)
    {
        // Prevent changing email verification for super admin (id = 1)
        if ($userId == 1) {
            session()->flash('error', 'Cannot modify super admin email verification status.');
            return;
        }

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
        $user = User::withTrashed()->findOrFail($userId);

        $this->selectedAdminId = $user->id;
        $this->selectedAdminData = [
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

        $this->originalAdminData = $this->selectedAdminData;
        $this->newPassword = '';
        $this->hasChanges = false;
    }

    public function updateAdmin()
    {
        $user = User::withTrashed()->findOrFail($this->selectedAdminId);

        // For super admin, allow updating all profile fields except role_id
        // Role, email verification, and account status are protected via other methods
        if ($this->selectedAdminId == 1) {
            // Update all profile fields for super admin
            $user->first_name = $this->selectedAdminData['first_name'];
            $user->last_name = $this->selectedAdminData['last_name'];
            $user->username = $this->selectedAdminData['username'];
            $user->email = $this->selectedAdminData['email'];
            $user->phone = $this->selectedAdminData['phone'];
            $user->bio = $this->selectedAdminData['bio'];
            $user->timezone = $this->selectedAdminData['timezone'];
            $user->facebook = $this->selectedAdminData['facebook'];
            $user->twitter = $this->selectedAdminData['twitter'];
            $user->linkedin = $this->selectedAdminData['linkedin'];
            $user->website = $this->selectedAdminData['website'];
            $user->github = $this->selectedAdminData['github'];
            $user->save();

            // Allow password update for super admin
            if (!empty($this->newPassword)) {
                $user->password = bcrypt($this->newPassword);
                $user->save();
                $this->newPassword = '';
            }
        } else {
            // For other admins, allow full update
            $user->update($this->selectedAdminData);

            if (!empty($this->newPassword)) {
                $user->password = bcrypt($this->newPassword);
                $user->save();
                $this->newPassword = '';
            }
        }

        session()->flash('message', 'Admin profile updated successfully.');
        $this->hasChanges = false;
        $this->originalAdminData = $this->selectedAdminData;
        $this->dispatch('close-modal');
    }

    public function updated($propertyName)
    {
        $this->hasChanges = $this->selectedAdminData !== $this->originalAdminData || !empty($this->newPassword);
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
                'role_id' => 1, // Admin
                'microsoft_account' => false,
                'timezone' => 'UTC',
                'email_verified_at' => null, // Initially unverified
            ]);

            session()->flash('message', 'Admin created successfully!');
            $this->closeCreateModal();
            $this->dispatch('closeCreateModal');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to create admin: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $adminsPaginated = $this->getAdminsProperty();
        
        return view('livewire.dashboard.admin', [
            'adminsPaginated' => $adminsPaginated,
            'admins' => $adminsPaginated->items(), // Array for the view loop
        ])->layout('components.layouts.dashboard');
    }
}
