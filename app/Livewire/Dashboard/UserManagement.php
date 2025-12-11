<?php

namespace App\Livewire\Dashboard;

use App\Models\User;
use App\Models\Setting;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserManagement extends Component
{
    use WithPagination, WithFileUploads;

    // Role filter: null = all roles, 1=Admin, 2=Instructor, 3=Student
    public $roleFilter = null;
    
    // User data properties
    public $usersWithRelations = [];
    public $selectedUserId;
    public $selectedUserData = [];
    public $selectedUserCourses = [];
    public $newPassword = '';
    public $originalUserData = [];
    public $hasChanges = false;
    
    // Search property
    public $search = '';
    
    // Sorting properties
    public $sortBy = 'first_name';
    public $sortDirection = 'asc';
    
    // Create new user properties
    public $showCreateModal = false;
    public $newUser = [
        'first_name' => '',
        'last_name' => '',
        'email' => '',
        'phone' => '',
        'password' => '',
        'password_confirmation' => '',
        'role_id' => 3, // Default to Student
    ];
    
    // Import properties
    public $showImportModal = false;
    public $importFile;
    public $importErrors = [];
    public $importSuccessCount = 0;
    public $importErrorCount = 0;

    protected $paginationTheme = 'bootstrap';

    public function mount()
    {
        // No role filtering by default - show all users
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingRoleFilter()
    {
        $this->resetPage();
    }

    public function getRoleName($roleId)
    {
        $roles = [1 => 'Admin', 2 => 'Instructor', 3 => 'Student'];
        return $roles[$roleId] ?? 'Unknown';
    }

    public function applySort($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    protected function getUsersProperty()
    {
        $perPage = (int) Setting::get('pagination_users_per_page', 15);
        
        $query = User::withTrashed();

        // Apply role filter if set
        if ($this->roleFilter !== null) {
            $query->where('role_id', $this->roleFilter);
        }

        // Load relationships for all users (we'll handle display in view)
        $query->with(['bookings.course', 'courses']);

        // Apply search filter
        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('first_name', 'like', '%' . $this->search . '%')
                  ->orWhere('last_name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%')
                  ->orWhere('phone', 'like', '%' . $this->search . '%');
            });
        }

        // Apply sorting
        if ($this->sortBy === 'name') {
            $query->orderBy('first_name', $this->sortDirection)
                  ->orderBy('last_name', $this->sortDirection);
        } elseif ($this->sortBy === 'email_verification') {
            if ($this->sortDirection === 'asc') {
                $query->orderByRaw('email_verified_at IS NULL ASC')
                      ->orderBy('email_verified_at', 'ASC');
            } else {
                $query->orderByRaw('email_verified_at IS NULL DESC')
                      ->orderBy('email_verified_at', 'DESC');
            }
        } elseif ($this->sortBy === 'status') {
            if ($this->sortDirection === 'asc') {
                $query->orderByRaw('deleted_at IS NULL DESC')
                      ->orderBy('deleted_at', 'ASC');
            } else {
                $query->orderByRaw('deleted_at IS NULL ASC')
                      ->orderBy('deleted_at', 'DESC');
            }
        } else {
            $query->orderBy($this->sortBy, $this->sortDirection);
        }

        $users = $query->paginate($perPage);
        
        // Ensure super admin (id = 1) is never soft-deleted
        $superAdmin = $users->firstWhere('id', 1);
        if ($superAdmin && $superAdmin->trashed()) {
            $superAdmin->restore();
        }
        
        return $users;
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
    }

    public function unblockUser($userId)
    {
        // Prevent unblocking super admin (id = 1)
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
            
            // Determine role change message (lower role_id = higher privilege: 1=Admin, 2=Instructor, 3=Student)
            $roleNames = [1 => 'Admin', 2 => 'Instructor', 3 => 'Student'];
            $newRoleName = $roleNames[$newRoleId] ?? 'User';
            $oldRoleName = $roleNames[$oldRoleId] ?? 'User';
            
            $action = $newRoleId < $oldRoleId ? 'promoted' : ($newRoleId > $oldRoleId ? 'demoted' : 'updated');
            session()->flash('message', "User has been {$action} from {$oldRoleName} to {$newRoleName}.");
        }
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
                $user->email_verified_at = null;
                session()->flash('message', 'User email verification has been deactivated.');
            } else {
                $user->email_verified_at = now();
                session()->flash('message', 'User email verification has been activated.');
            }
            $user->save();
        }
    }

    public function showDetails($userId)
    {
        $user = User::withTrashed()
            ->with(['bookings.course', 'courses'])
            ->findOrFail($userId);

        $this->selectedUserId = $user->id;
        $this->selectedUserData = [
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

        // Load courses based on user role
        if ($user->role_id == 3) {
            $this->selectedUserCourses = $user->bookings->pluck('course')->filter();
        } elseif ($user->role_id == 2) {
            $this->selectedUserCourses = $user->courses;
        } else {
            $this->selectedUserCourses = collect();
        }
        
        $this->originalUserData = $this->selectedUserData;
        $this->newPassword = '';
        $this->hasChanges = false;
    }

    public function updateUser()
    {
        $user = User::withTrashed()->findOrFail($this->selectedUserId);

        // For super admin, allow updating all profile fields except role_id
        if ($this->selectedUserId == 1) {
            $user->first_name = $this->selectedUserData['first_name'];
            $user->last_name = $this->selectedUserData['last_name'];
            $user->username = $this->selectedUserData['username'];
            $user->email = $this->selectedUserData['email'];
            $user->phone = $this->selectedUserData['phone'];
            $user->bio = $this->selectedUserData['bio'];
            $user->timezone = $this->selectedUserData['timezone'];
            $user->facebook = $this->selectedUserData['facebook'];
            $user->twitter = $this->selectedUserData['twitter'];
            $user->linkedin = $this->selectedUserData['linkedin'];
            $user->website = $this->selectedUserData['website'];
            $user->github = $this->selectedUserData['github'];
            $user->save();

            if (!empty($this->newPassword)) {
                $user->password = bcrypt($this->newPassword);
                $user->save();
                $this->newPassword = '';
            }
        } else {
            $user->update($this->selectedUserData);

            if (!empty($this->newPassword)) {
                $user->password = bcrypt($this->newPassword);
                $user->save();
                $this->newPassword = '';
            }
        }

        $roleName = $this->getRoleName($user->role_id);
        session()->flash('message', ucfirst($roleName) . ' profile updated successfully.');
        $this->hasChanges = false;
        $this->originalUserData = $this->selectedUserData;
        $this->dispatch('close-modal');
    }

    public function updated($propertyName)
    {
        $this->hasChanges = $this->selectedUserData !== $this->originalUserData || !empty($this->newPassword);
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
            'role_id' => 3, // Default to Student
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
            'newUser.role_id' => 'required|in:1,2,3',
        ], [
            'newUser.password.confirmed' => 'The password confirmation does not match.',
            'newUser.role_id.required' => 'Please select a role.',
            'newUser.role_id.in' => 'Invalid role selected.',
        ]);

        try {
            $user = User::create([
                'first_name' => $this->newUser['first_name'],
                'last_name' => $this->newUser['last_name'],
                'email' => $this->newUser['email'],
                'phone' => $this->newUser['phone'],
                'password' => Hash::make($this->newUser['password']),
                'role_id' => $this->newUser['role_id'],
                'microsoft_account' => false,
                'timezone' => 'UTC',
                'email_verified_at' => null, // Initially unverified
            ]);

            $roleName = $this->getRoleName($this->newUser['role_id']);
            session()->flash('message', ucfirst($roleName) . ' created successfully!');
            $this->closeCreateModal();
            $this->dispatch('closeCreateModal');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to create user: ' . $e->getMessage());
        }
    }

    public function exportUsers()
    {
        $query = User::withTrashed();

        // Apply role filter if set
        if ($this->roleFilter !== null) {
            $query->where('role_id', $this->roleFilter);
        }

        // Load relationships
        $query->with(['bookings.course', 'courses']);

        // Apply search filter if exists
        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('first_name', 'like', '%' . $this->search . '%')
                  ->orWhere('last_name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%')
                  ->orWhere('phone', 'like', '%' . $this->search . '%');
            });
        }

        // Apply sorting
        if ($this->sortBy === 'name') {
            $query->orderBy('first_name', $this->sortDirection)
                  ->orderBy('last_name', $this->sortDirection);
        } elseif ($this->sortBy === 'role_id') {
            $query->orderBy('role_id', $this->sortDirection);
        } elseif ($this->sortBy === 'email_verification') {
            if ($this->sortDirection === 'asc') {
                $query->orderByRaw('email_verified_at IS NULL ASC')
                      ->orderBy('email_verified_at', 'ASC');
            } else {
                $query->orderByRaw('email_verified_at IS NULL DESC')
                      ->orderBy('email_verified_at', 'DESC');
            }
        } elseif ($this->sortBy === 'status') {
            if ($this->sortDirection === 'asc') {
                $query->orderByRaw('deleted_at IS NULL DESC')
                      ->orderBy('deleted_at', 'ASC');
            } else {
                $query->orderByRaw('deleted_at IS NULL ASC')
                      ->orderBy('deleted_at', 'DESC');
            }
        } else {
            $query->orderBy($this->sortBy, $this->sortDirection);
        }

        $users = $query->get();

        $filename = 'users_export_' . date('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () use ($users) {
            $file = fopen('php://output', 'w');

            // Add BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Headers
            $headers = [
                'First Name',
                'Last Name',
                'Email',
                'Phone',
                'Username',
                'Role',
                'Email Verification Status',
                'Account Status',
                'Bio',
                'Timezone',
                'Enrolled Courses Count',
                'Enrolled Courses',
                'Courses Created Count',
                'Courses Created',
            ];

            fputcsv($file, $headers);

            // Data rows
            foreach ($users as $user) {
                $enrolledCourses = $user->bookings->pluck('course')->filter();
                $createdCourses = $user->courses;
                
                $row = [
                    $user->first_name ?? '',
                    $user->last_name ?? '',
                    $user->email ?? '',
                    $user->phone ?? '',
                    $user->username ?? '',
                    $this->getRoleName($user->role_id),
                    $user->hasVerifiedEmail() ? 'Verified' : 'Unverified',
                    $user->trashed() ? 'Blocked' : 'Active',
                    $user->bio ?? '',
                    $user->timezone ?? 'UTC',
                    $enrolledCourses->count(),
                    $enrolledCourses->pluck('title')->implode(', '),
                    $createdCourses->count(),
                    $createdCourses->pluck('title')->implode(', '),
                ];

                fputcsv($file, $row);
            }

            fclose($file);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function openImportModal()
    {
        $this->showImportModal = true;
        $this->importFile = null;
        $this->importErrors = [];
        $this->importSuccessCount = 0;
        $this->importErrorCount = 0;
        $this->resetErrorBag();
    }

    public function closeImportModal()
    {
        $this->showImportModal = false;
        $this->importFile = null;
        $this->importErrors = [];
        $this->importSuccessCount = 0;
        $this->importErrorCount = 0;
        $this->resetErrorBag();
    }

    public function importUsers()
    {
        $this->validate([
            'importFile' => 'required|mimes:csv,txt|max:10240',
        ], [
            'importFile.required' => 'Please select a CSV file to import.',
            'importFile.mimes' => 'The file must be a CSV file.',
            'importFile.max' => 'The file size must not exceed 10MB.',
        ]);

        try {
            $file = fopen($this->importFile->getRealPath(), 'r');
            
            // Skip BOM if present
            $firstLine = fgets($file);
            if (substr($firstLine, 0, 3) === "\xEF\xBB\xBF") {
                rewind($file);
                fread($file, 3);
            } else {
                rewind($file);
            }

            $headers = fgetcsv($file);
            if (!$headers) {
                session()->flash('error', 'Invalid CSV file format.');
                return;
            }

            $headers = array_map(function($h) {
                return strtolower(trim($h));
            }, $headers);

            $expectedHeaders = ['first name', 'last name', 'email', 'phone', 'username', 'role', 'bio', 'timezone', 'email verification status'];
            $headerMap = [];
            foreach ($expectedHeaders as $expected) {
                foreach ($headers as $index => $header) {
                    if (strtolower(trim($header)) === $expected) {
                        $headerMap[$expected] = $index;
                        break;
                    }
                }
            }

            if (!isset($headerMap['email'])) {
                session()->flash('error', 'CSV file must contain an "Email" column.');
                fclose($file);
                return;
            }

            $this->importErrors = [];
            $this->importSuccessCount = 0;
            $this->importErrorCount = 0;
            $rowNumber = 1;

            while (($row = fgetcsv($file)) !== false) {
                $rowNumber++;
                
                if (empty(array_filter($row))) {
                    continue;
                }

                $emailVerificationStatus = '';
                if (isset($headerMap['email verification status']) && isset($row[$headerMap['email verification status']])) {
                    $emailVerificationStatus = strtolower(trim($row[$headerMap['email verification status']]));
                }
                
                $data = [
                    'first_name' => isset($headerMap['first name']) && isset($row[$headerMap['first name']]) ? trim($row[$headerMap['first name']]) : '',
                    'last_name' => isset($headerMap['last name']) && isset($row[$headerMap['last name']]) ? trim($row[$headerMap['last name']]) : '',
                    'email' => isset($row[$headerMap['email']]) ? trim($row[$headerMap['email']]) : '',
                    'phone' => isset($headerMap['phone']) && isset($row[$headerMap['phone']]) ? trim($row[$headerMap['phone']]) : '',
                    'username' => isset($headerMap['username']) && isset($row[$headerMap['username']]) ? trim($row[$headerMap['username']]) : '',
                    'bio' => isset($headerMap['bio']) && isset($row[$headerMap['bio']]) ? trim($row[$headerMap['bio']]) : '',
                    'timezone' => isset($headerMap['timezone']) && isset($row[$headerMap['timezone']]) ? trim($row[$headerMap['timezone']]) : 'UTC',
                    'email_verification_status' => $emailVerificationStatus,
                ];

                $validator = Validator::make($data, [
                    'first_name' => 'required|string|max:20',
                    'last_name' => 'required|string|max:20',
                    'email' => 'required|email|max:100|unique:users,email',
                    'phone' => 'nullable|string|max:20|unique:users,phone',
                    'username' => 'nullable|string|max:20|unique:users,username',
                    'bio' => 'nullable|string|max:255',
                    'timezone' => 'nullable|string|timezone',
                ]);

                if ($validator->fails()) {
                    $this->importErrorCount++;
                    $this->importErrors[] = [
                        'row' => $rowNumber,
                        'email' => $data['email'] ?: 'N/A',
                        'errors' => $validator->errors()->all()
                    ];
                    continue;
                }

                try {
                    $emailVerifiedAt = null;
                    if (!empty($data['email_verification_status']) && strtolower($data['email_verification_status']) === 'verified') {
                        $emailVerifiedAt = now();
                    }
                    
                    // Determine role from CSV or default to Student
                    $roleId = 3; // Default to Student
                    if (isset($headerMap['role']) && isset($row[$headerMap['role']])) {
                        $roleString = strtolower(trim($row[$headerMap['role']]));
                        $roleMap = ['admin' => 1, 'instructor' => 2, 'student' => 3];
                        if (isset($roleMap[$roleString])) {
                            $roleId = $roleMap[$roleString];
                        }
                    } elseif ($this->roleFilter !== null) {
                        $roleId = $this->roleFilter;
                    }
                    
                    User::create([
                        'first_name' => $data['first_name'],
                        'last_name' => $data['last_name'],
                        'email' => $data['email'],
                        'phone' => $data['phone'] ?: null,
                        'username' => $data['username'] ?: null,
                        'bio' => $data['bio'] ?: null,
                        'timezone' => $data['timezone'] ?: 'UTC',
                        'password' => Hash::make('password123'),
                        'role_id' => $roleId,
                        'microsoft_account' => false,
                        'email_verified_at' => $emailVerifiedAt,
                    ]);
                    $this->importSuccessCount++;
                } catch (\Exception $e) {
                    $this->importErrorCount++;
                    $this->importErrors[] = [
                        'row' => $rowNumber,
                        'email' => $data['email'] ?: 'N/A',
                        'errors' => ['Failed to create user: ' . $e->getMessage()]
                    ];
                }
            }

            fclose($file);

            if ($this->importSuccessCount > 0) {
                session()->flash('message', "Successfully imported {$this->importSuccessCount} user(s).");
                if ($this->importErrorCount == 0) {
                    $this->closeImportModal();
                    $this->dispatch('close-import-modal');
                }
            }
            if ($this->importErrorCount > 0) {
                session()->flash('warning', "Failed to import {$this->importErrorCount} user(s). Please check the errors below.");
            }

            $this->resetPage();
        } catch (\Exception $e) {
            session()->flash('error', 'Import failed: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $usersPaginated = $this->getUsersProperty();
        
        $this->usersWithRelations = [];
        foreach ($usersPaginated as $user) {
            $enrolledCourses = $user->bookings->pluck('course')->filter();
            $createdCourses = $user->courses;
            
            $this->usersWithRelations[] = [
                'user' => $user,
                'enrolled_courses' => $enrolledCourses,
                'created_courses' => $createdCourses,
                'enrolled_count' => $enrolledCourses->count(),
                'created_count' => $createdCourses->count(),
            ];
        }
        
        return view('livewire.dashboard.user-management', [
            'users' => $usersPaginated,
        ])->layout('components.layouts.dashboard');
    }
}

