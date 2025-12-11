<div class="users py-5 px-4">
    <div class="mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="text-primary fw-bold mb-0">Users Management</h4>
            <div class="d-flex gap-2">
                <button type="button" wire:click="exportUsers" class="btn btn-success">
                    <i class="bi bi-download me-2"></i>Export CSV
                </button>
                <button type="button" wire:click="openImportModal" class="btn btn-info text-white" data-bs-toggle="modal"
                    data-bs-target="#importUserModal">
                    <i class="bi bi-upload me-2"></i>Import CSV
                </button>
                <button class="btn btn-primary" wire:click="openCreateModal" data-bs-toggle="modal"
                    data-bs-target="#createUserModal">
                    <i class="bi bi-plus-circle me-2"></i>Add New User
                </button>
            </div>
        </div>

        {{-- Flash Messages --}}
        @if (session()->has('message'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session()->has('warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>{{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Help Text - Only show if there are unverified users --}}
        @php
            $hasUnverifiedUsers = collect($usersWithRelations)->contains(function ($data) {
                return !$data['user']->hasVerifiedEmail();
            });
        @endphp
        @if ($hasUnverifiedUsers)
            <div class="alert alert-info mb-3">
                <small>
                    <i class="bi bi-info-circle me-1"></i>
                    <strong>Note:</strong> 
                    <strong>Email Verification</strong> controls whether a user can log in (email verification status). 
                    <strong>Account Status</strong> controls whether the account is active or blocked (account access).
                </small>
            </div>
        @endif

        {{-- Search and Filter Bar --}}
        <div class="mb-3">
            <div class="row">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" wire:model.live.debounce.300ms="search"
                            placeholder="Search by name, email, or phone...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select" wire:model.live="roleFilter">
                        <option value="">All Roles</option>
                        <option value="1">Admin</option>
                        <option value="2">Instructor</option>
                        <option value="3">Student</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="table-wrapper">
            <div class="table-responsive ">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col" style="cursor: pointer;" wire:click.prevent='applySort("name")' class="user-select-none">
                                Name
                                <span class="ms-1">
                                    @if ($sortBy === 'name')
                                        @if ($sortDirection === 'asc')
                                            <i class="bi bi-arrow-up"></i>
                                        @else
                                            <i class="bi bi-arrow-down"></i>
                                        @endif
                                    @else
                                        <i class="bi bi-arrows-vertical text-muted opacity-50"></i>
                                    @endif
                                </span>
                            </th>
                            <th scope="col">Email</th>
                            <th scope="col" style="cursor: pointer;" wire:click.prevent='applySort("role_id")' class="user-select-none">
                                Role
                                <span class="ms-1">
                                    @if ($sortBy === 'role_id')
                                        @if ($sortDirection === 'asc')
                                            <i class="bi bi-arrow-up"></i>
                                        @else
                                            <i class="bi bi-arrow-down"></i>
                                        @endif
                                    @else
                                        <i class="bi bi-arrows-vertical text-muted opacity-50"></i>
                                    @endif
                                </span>
                            </th>
                            <th scope="col" style="cursor: pointer;" wire:click.prevent='applySort("email_verification")' class="user-select-none">
                                Email Verification
                                <span class="ms-1">
                                    @if ($sortBy === 'email_verification')
                                        @if ($sortDirection === 'asc')
                                            <i class="bi bi-arrow-up"></i>
                                        @else
                                            <i class="bi bi-arrow-down"></i>
                                        @endif
                                    @else
                                        <i class="bi bi-arrows-vertical text-muted opacity-50"></i>
                                    @endif
                                </span>
                            </th>
                            <th scope="col" style="cursor: pointer;" wire:click.prevent='applySort("status")' class="user-select-none">
                                Account Status
                                <span class="ms-1">
                                    @if ($sortBy === 'status')
                                        @if ($sortDirection === 'asc')
                                            <i class="bi bi-arrow-up"></i>
                                        @else
                                            <i class="bi bi-arrow-down"></i>
                                        @endif
                                    @else
                                        <i class="bi bi-arrows-vertical text-muted opacity-50"></i>
                                    @endif
                                </span>
                            </th>
                            <th scope="col">Change Role</th>
                            <th scope="col">Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($usersWithRelations as $index => $data)
                            @php $user = $data['user']; @endphp
                            <tr class="{{ $user->trashed() && $user->id != 1 ? 'table-danger' : '' }} {{ $user->id == 1 ? 'table-info' : '' }}">
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    {{ $user->name }}
                                    @if ($user->id == 1)
                                        <span class="badge bg-primary ms-2">
                                            <i class="bi bi-shield-check me-1"></i> Super Admin
                                        </span>
                                    @endif
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @if ($user->role_id == 1)
                                        <span class="badge bg-danger">Admin</span>
                                    @elseif ($user->role_id == 2)
                                        <span class="badge bg-warning text-dark">Instructor</span>
                                    @else
                                        <span class="badge bg-info">Student</span>
                                    @endif
                                </td>
                                {{-- Email Verification Status --}}
                                <td>
                                    @if ($user->id == 1)
                                        <span class="badge bg-secondary">
                                            <i class="bi bi-shield-lock me-1"></i> Protected
                                        </span>
                                    @else
                                        <div class="dropdown">
                                            <button
                                                class="btn btn-sm {{ $user->hasVerifiedEmail() ? 'btn-outline-success' : 'btn-outline-warning' }} dropdown-toggle"
                                                type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                @if ($user->hasVerifiedEmail())
                                                    <i class="bi bi-check-circle me-1"></i> Verified
                                                @else
                                                    <i class="bi bi-x-circle me-1"></i> Not Verified
                                                @endif
                                            </button>
                                            <ul class="dropdown-menu shadow-sm">
                                                @if ($user->hasVerifiedEmail())
                                                    <li>
                                                        <a href="#" class="dropdown-item text-warning fw-semibold"
                                                            wire:click.prevent="toggleEmailVerification({{ $user->id }})"
                                                            wire:loading.attr="disabled">
                                                            <span wire:loading.remove>
                                                                <i class="bi bi-x-circle me-2"></i> Mark as Unverified
                                                            </span>
                                                            <span wire:loading>
                                                                <span class="spinner-border spinner-border-sm me-2"></span> Processing...
                                                            </span>
                                                        </a>
                                                    </li>
                                                @else
                                                    <li>
                                                        <a href="#" class="dropdown-item text-success fw-semibold"
                                                            wire:click.prevent="toggleEmailVerification({{ $user->id }})"
                                                            wire:loading.attr="disabled">
                                                            <span wire:loading.remove>
                                                                <i class="bi bi-check-circle me-2"></i> Mark as Verified
                                                            </span>
                                                            <span wire:loading>
                                                                <span class="spinner-border spinner-border-sm me-2"></span> Processing...
                                                            </span>
                                                        </a>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    @endif
                                </td>
                                {{-- Account Status (Block/Unblock) --}}
                                <td>
                                    @if ($user->id == 1)
                                        <span class="badge bg-secondary">
                                            <i class="bi bi-shield-lock me-1"></i> Protected
                                        </span>
                                    @else
                                        <div class="dropdown">
                                            <button
                                                class="btn btn-sm {{ $user->trashed() ? 'btn-outline-danger' : 'btn-outline-success' }} dropdown-toggle"
                                                type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                @if ($user->trashed())
                                                    <i class="bi bi-lock me-1"></i> Blocked
                                                @else
                                                    <i class="bi bi-unlock me-1"></i> Active
                                                @endif
                                            </button>
                                            <ul class="dropdown-menu shadow-sm">
                                                @if ($user->trashed())
                                                    <li>
                                                        <a href="#" class="dropdown-item text-success fw-semibold"
                                                            wire:click.prevent="unblockUser({{ $user->id }})"
                                                            wire:loading.attr="disabled">
                                                            <span wire:loading.remove>
                                                                <i class="bi bi-unlock me-2"></i> Unblock Account
                                                            </span>
                                                            <span wire:loading>
                                                                <span class="spinner-border spinner-border-sm me-2"></span> Processing...
                                                            </span>
                                                        </a>
                                                    </li>
                                                @else
                                                    <li>
                                                        <a href="#" class="dropdown-item text-danger fw-semibold"
                                                            wire:click.prevent="blockUser({{ $user->id }})"
                                                            wire:loading.attr="disabled">
                                                            <span wire:loading.remove>
                                                                <i class="bi bi-lock me-2"></i> Block Account
                                                            </span>
                                                            <span wire:loading>
                                                                <span class="spinner-border spinner-border-sm me-2"></span> Processing...
                                                            </span>
                                                        </a>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    @endif
                                </td>

                                {{-- Role Change Dropdown --}}
                                <td>
                                    @if ($user->id == 1)
                                        <span class="badge bg-secondary">
                                            <i class="bi bi-shield-lock me-1"></i> Protected
                                        </span>
                                    @else
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                Change Role
                                            </button>
                                            <ul class="dropdown-menu shadow-sm">
                                                @if ($user->role_id !== 1)
                                                    <li>
                                                        <a href="#" class="dropdown-item text-danger fw-semibold"
                                                            wire:click.prevent="promoteUser({{ $user->id }}, 1)">
                                                            <i class="fa-solid fa-user-tie me-2"></i> Admin
                                                        </a>
                                                    </li>
                                                @endif
                                                @if ($user->role_id !== 2)
                                                    <li>
                                                        <a href="#" class="dropdown-item text-warning fw-semibold"
                                                            wire:click.prevent="promoteUser({{ $user->id }}, 2)">
                                                            <i class="bi bi-person-gear me-2"></i> Instructor
                                                        </a>
                                                    </li>
                                                @endif
                                                @if ($user->role_id !== 3)
                                                    <li>
                                                        <a href="#" class="dropdown-item text-info fw-semibold"
                                                            wire:click.prevent="promoteUser({{ $user->id }}, 3)">
                                                            <i class="bi bi-person me-2"></i> Student
                                                        </a>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-primary" type="button" data-bs-toggle="modal"
                                        data-bs-target="#staticBackdrop" wire:click="showDetails({{ $user->id }})"
                                        aria-label="View user details">
                                        Details
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-3">
                                    <i class="bi bi-emoji-frown me-2"></i> No users found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        <div class="mt-3">
            {{ $users->links() }}
        </div>
    </div>
    
    {{-- User Details Modal --}}
    <div wire:ignore.self class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false"
        tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5 text-white">
                        <i class="bi bi-person-lines-fill me-2 text-white"></i> User Details
                        @if ($selectedUserId == 1)
                            <span class="badge bg-primary ms-2">
                                <i class="bi bi-shield-check me-1"></i> Super Admin
                            </span>
                        @endif
                    </h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if ($selectedUserId == 1)
                        <div class="alert alert-info mb-3">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Super Admin:</strong> You can edit all profile details (name, email, bio, etc.) for the super admin. However, Email Verification, Account Status, and Role changes are protected.
                        </div>
                    @endif
                    <form wire:submit.prevent="updateUser">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label>First Name</label>
                                <input type="text" class="form-control"
                                    wire:model.defer="selectedUserData.first_name">
                            </div>
                            <div class="col-md-6">
                                <label>Last Name</label>
                                <input type="text" class="form-control"
                                    wire:model.defer="selectedUserData.last_name">
                            </div>
                            <div class="col-md-6">
                                <label>Username</label>
                                <input type="text" class="form-control"
                                    wire:model.defer="selectedUserData.username">
                            </div>
                            <div class="col-md-6">
                                <label>Email</label>
                                <input type="email" class="form-control" wire:model.defer="selectedUserData.email">
                            </div>
                            <div class="col-md-6">
                                <label>Phone</label>
                                <input type="text" class="form-control" wire:model.defer="selectedUserData.phone">
                            </div>
                            <div class="col-md-6">
                                <label>Timezone</label>
                                <input type="text" class="form-control"
                                    wire:model.defer="selectedUserData.timezone">
                            </div>
                            <div class="col-md-6">
                                <label>New Password <small class="text-muted">(leave blank to keep current)</small></label>
                                <input type="password" class="form-control" wire:model.defer="newPassword">
                            </div>
                            <div class="col-12">
                                <label>Bio</label>
                                <textarea class="form-control" rows="2" wire:model.defer="selectedUserData.bio"></textarea>
                            </div>
                            <div class="col-md-6">
                                <label>Microsoft Account</label><br>
                                @if (data_get($selectedUserData, 'microsoft_account'))
                                    <span class="badge bg-success">Connected</span>
                                @else
                                    <span class="badge bg-secondary">Not Connected</span>
                                @endif
                            </div>

                            <div class="col-12">
                                <h6 class="mt-3 text-primary fw-bold">Social Links</h6>
                            </div>
                            <div class="col-md-6">
                                <label>Facebook</label>
                                <input type="text" class="form-control"
                                    wire:model.defer="selectedUserData.facebook">
                            </div>
                            <div class="col-md-6">
                                <label>Twitter</label>
                                <input type="text" class="form-control"
                                    wire:model.defer="selectedUserData.twitter">
                            </div>
                            <div class="col-md-6">
                                <label>LinkedIn</label>
                                <input type="text" class="form-control"
                                    wire:model.defer="selectedUserData.linkedin">
                            </div>
                            <div class="col-md-6">
                                <label>Website</label>
                                <input type="text" class="form-control"
                                    wire:model.defer="selectedUserData.website">
                            </div>
                            <div class="col-md-6">
                                <label>GitHub</label>
                                <input type="text" class="form-control"
                                    wire:model.defer="selectedUserData.github">
                            </div>
                            @if (count($selectedUserCourses) > 0)
                                <div class="col-12 mt-4">
                                    <h6 class="text-primary fw-bold">
                                        @php
                                            $selectedUser = \App\Models\User::withTrashed()->find($selectedUserId);
                                        @endphp
                                        @if ($selectedUser && $selectedUser->role_id == 3)
                                            Enrolled Courses
                                        @elseif ($selectedUser && $selectedUser->role_id == 2)
                                            Courses Created
                                        @else
                                            Courses
                                        @endif
                                    </h6>
                                    <ul class="list-group">
                                        @forelse($selectedUserCourses as $course)
                                            <li class="list-group-item">{{ $course->title }}</li>
                                        @empty
                                            <li class="list-group-item text-muted">No courses.</li>
                                        @endforelse
                                    </ul>
                                </div>
                            @endif
                        </div>
                        <div class="modal-footer mt-4">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Update Profile</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Create New User Modal --}}
    <div wire:ignore.self class="modal fade" id="createUserModal" data-bs-backdrop="static" data-bs-keyboard="false"
        tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5 text-white">
                        <i class="bi bi-person-plus me-2 text-white"></i> Add New User
                    </h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        wire:click="closeCreateModal"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="createUser">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label>First Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('newUser.first_name') is-invalid @enderror"
                                    wire:model.defer="newUser.first_name">
                                @error('newUser.first_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label>Last Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('newUser.last_name') is-invalid @enderror"
                                    wire:model.defer="newUser.last_name">
                                @error('newUser.last_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label>Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('newUser.email') is-invalid @enderror"
                                    wire:model.defer="newUser.email">
                                @error('newUser.email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label>Phone <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('newUser.phone') is-invalid @enderror"
                                    wire:model.defer="newUser.phone">
                                @error('newUser.phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label>Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control @error('newUser.password') is-invalid @enderror"
                                    wire:model.defer="newUser.password">
                                @error('newUser.password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label>Confirm Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control"
                                    wire:model.defer="newUser.password_confirmation">
                            </div>
                            <div class="col-md-6">
                                <label>Role <span class="text-danger">*</span></label>
                                <select class="form-select @error('newUser.role_id') is-invalid @enderror"
                                    wire:model.defer="newUser.role_id">
                                    <option value="">Select Role</option>
                                    <option value="3">Student</option>
                                    <option value="2">Instructor</option>
                                    <option value="1">Admin</option>
                                </select>
                                @error('newUser.role_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer mt-4">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                                wire:click="closeCreateModal">Close</button>
                            <button type="submit" class="btn btn-primary">
                                <span wire:loading.remove wire:target="createUser">Create User</span>
                                <span wire:loading wire:target="createUser">
                                    <span class="spinner-border spinner-border-sm me-2"></span>Creating...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Import Users Modal --}}
    <div wire:ignore.self class="modal fade" id="importUserModal" data-bs-backdrop="static" data-bs-keyboard="false"
        tabindex="-1" aria-labelledby="importUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importUserModalLabel">
                        <i class="bi bi-upload me-2"></i>Import Users from CSV
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" wire:click="closeImportModal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>CSV Format:</strong> Your CSV file should contain the following columns:
                        <ul class="mb-0 mt-2">
                            <li><strong>First Name</strong> (required)</li>
                            <li><strong>Last Name</strong> (required)</li>
                            <li><strong>Email</strong> (required, must be unique)</li>
                            <li><strong>Phone</strong> (optional, must be unique if provided)</li>
                            <li><strong>Username</strong> (optional, must be unique if provided)</li>
                            <li><strong>Role</strong> (optional: "Admin", "Instructor", or "Student", defaults to "Student")</li>
                            <li><strong>Bio</strong> (optional)</li>
                            <li><strong>Timezone</strong> (optional, defaults to UTC)</li>
                            <li><strong>Email Verification Status</strong> (optional: "Verified" or "Unverified", defaults to "Unverified")</li>
                        </ul>
                    </div>

                    <form wire:submit.prevent="importUsers">
                        <div class="mb-3">
                            <label for="importFile" class="form-label">Select CSV File <span class="text-danger">*</span></label>
                            <input type="file" class="form-control @error('importFile') is-invalid @enderror" 
                                id="importFile" wire:model="importFile" accept=".csv,.txt">
                            @error('importFile')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Maximum file size: 10MB</small>
                        </div>

                        @if ($importFile)
                            <div class="alert alert-success">
                                <i class="bi bi-check-circle me-2"></i>File selected: {{ $importFile->getClientOriginalName() }}
                            </div>
                        @endif

                        @if (count($importErrors) > 0)
                            <div class="alert alert-danger mt-3">
                                <h6 class="fw-bold">Import Errors ({{ count($importErrors) }}):</h6>
                                <div style="max-height: 200px; overflow-y: auto;">
                                    @foreach ($importErrors as $error)
                                        <div class="mb-2">
                                            <strong>Row {{ $error['row'] }}</strong> (Email: {{ $error['email'] }}):
                                            <ul class="mb-0">
                                                @foreach ($error['errors'] as $err)
                                                    <li>{{ $err }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="modal-footer mt-4">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                                wire:click="closeImportModal">Close</button>
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                <span wire:loading.remove>
                                    <i class="bi bi-upload me-2"></i>Import Users
                                </span>
                                <span wire:loading>
                                    <span class="spinner-border spinner-border-sm me-2"></span> Importing...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@push('js')
    <script>
        window.addEventListener('close-modal', event => {
            $('#staticBackdrop').modal('hide');
            $('#createUserModal').modal('hide');
        });
        
        Livewire.on('closeCreateModal', () => {
            $('#createUserModal').modal('hide');
        });
        
        window.addEventListener('close-import-modal', event => {
            $('#importUserModal').modal('hide');
        });
    </script>
@endpush

