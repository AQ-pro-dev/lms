<div class="instructors py-5 px-4">
    <div class="mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="text-primary fw-bold mb-0">Instructors and Their Courses</h4>
            <button class="btn btn-primary" wire:click="openCreateModal" data-bs-toggle="modal"
                data-bs-target="#createInstructorModal">
                <i class="bi bi-plus-circle me-2"></i>Add New Instructor
            </button>
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

        {{-- Search Bar --}}
        <div class="mb-3">
            <div class="row">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" wire:model.live.debounce.300ms="search"
                            placeholder="Search by name, email, or phone...">
                    </div>
                </div>
            </div>
        </div>

        <div class="table-wrapper">
            <div class="table-responsive ">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Instructor Name</th>
                            <th scope="col">Email</th>
                            <th scope="col">Courses Created</th>
                            <th scope="col">Email Verification</th>
                            <th scope="col">Account Status</th>
                            <th scope="col">Promote</th>
                            <th scope="col">Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($instructorsWithCourses as $index => $data)
                            @php $user = $data['instructor']; @endphp
                            <tr class="{{ $user->trashed() ? 'table-danger' : '' }}">
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $data['count'] }}</td>
                                {{-- Email Verification Status --}}
                                <td>
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
                                </td>
                                {{-- Account Status (Block/Unblock) --}}
                                <td>
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
                                </td>

                                {{-- Promote Dropdown --}}
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            Promote To
                                        </button>
                                        <ul class="dropdown-menu shadow-sm">
                                            @if ($user->role_id !== 3)
                                                <li>
                                                    <a href="#" class="dropdown-item text-primary fw-semibold"
                                                        wire:click.prevent="promoteUser({{ $user->id }}, 3)">
                                                        <i class="bi bi-person me-2"></i> Student
                                                    </a>
                                                </li>
                                            @endif
                                            @if ($user->role_id !== 1)
                                                <li>
                                                    <a href="#" class="dropdown-item text-primary fw-semibold"
                                                        wire:click.prevent="promoteUser({{ $user->id }}, 1)">
                                                        <i class="fa-solid fa-user-tie me-2"></i> Admin
                                                    </a>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-primary" type="button" data-bs-toggle="modal"
                                        data-bs-target="#staticBackdrop" wire:click="showDetails({{ $user->id }})"
                                        aria-label="View instructor details">
                                        Details
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-3">
                                    <i class="bi bi-emoji-frown me-2"></i> No instructors found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        <div class="mt-3">
            {{ $instructors->links() }}
        </div>
    </div>
    {{-- instructor modal --}}
    <div wire:ignore.self class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false"
        tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5 text-white">
                        <i class="bi bi-person-lines-fill me-2 text-white"></i> Instructor Details
                    </h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="updateInstructor">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label>First Name</label>
                                <input type="text" class="form-control"
                                    wire:model.defer="selectedInstructorData.first_name">
                            </div>
                            <div class="col-md-6">
                                <label>Last Name</label>
                                <input type="text" class="form-control"
                                    wire:model.defer="selectedInstructorData.last_name">
                            </div>
                            <div class="col-md-6">
                                <label>Username</label>
                                <input type="text" class="form-control"
                                    wire:model.defer="selectedInstructorData.username">
                            </div>
                            <div class="col-md-6">
                                <label>Email</label>
                                <input type="email" class="form-control" wire:model.defer="selectedInstructorData.email">
                            </div>
                            <div class="col-md-6">
                                <label>Phone</label>
                                <input type="text" class="form-control" wire:model.defer="selectedInstructorData.phone">
                            </div>
                            <div class="col-md-6">
                                <label>Timezone</label>
                                <input type="text" class="form-control"
                                    wire:model.defer="selectedInstructorData.timezone">
                            </div>
                            <div class="col-md-6">
                                <label>New Password <small class="text-muted">(leave blank to keep
                                        current)</small></label>
                                <input type="password" class="form-control" wire:model.defer="newPassword">
                            </div>
                            <div class="col-12">
                                <label>Bio</label>
                                <textarea class="form-control" rows="2" wire:model.defer="selectedInstructorData.bio"></textarea>
                            </div>
                            <div class="col-md-6">
                                <label>Microsoft Account</label><br>
                                @if (data_get($selectedInstructorData, 'microsoft_account'))
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
                                    wire:model.defer="selectedInstructorData.facebook">
                            </div>
                            <div class="col-md-6">
                                <label>Twitter</label>
                                <input type="text" class="form-control"
                                    wire:model.defer="selectedInstructorData.twitter">
                            </div>
                            <div class="col-md-6">
                                <label>LinkedIn</label>
                                <input type="text" class="form-control"
                                    wire:model.defer="selectedInstructorData.linkedin">
                            </div>
                            <div class="col-md-6">
                                <label>Website</label>
                                <input type="text" class="form-control"
                                    wire:model.defer="selectedInstructorData.website">
                            </div>
                            <div class="col-md-6">
                                <label>GitHub</label>
                                <input type="text" class="form-control"
                                    wire:model.defer="selectedInstructorData.github">
                            </div>
                            <div class="col-12 mt-4">
                                <h6 class="text-primary fw-bold">Created Courses</h6>
                                <ul class="list-group">
                                    @forelse($selectedInstructorCourses as $course)
                                        <li class="list-group-item">{{ $course->title }}</li>
                                    @empty
                                        <li class="list-group-item text-muted">No courses created.</li>
                                    @endforelse
                                </ul>
                            </div>
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

    {{-- Create New Instructor Modal --}}
    <div wire:ignore.self class="modal fade" id="createInstructorModal" data-bs-backdrop="static" data-bs-keyboard="false"
        tabindex="-1" aria-labelledby="createInstructorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5 text-white">
                        <i class="bi bi-person-plus me-2 text-white"></i> Add New Instructor
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
                        </div>
                        <div class="modal-footer mt-4">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                                wire:click="closeCreateModal">Close</button>
                            <button type="submit" class="btn btn-primary">
                                <span wire:loading.remove wire:target="createUser">Create Instructor</span>
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
</div>
@push('js')
    <script>
        window.addEventListener('close-modal', event => {
            $('#staticBackdrop').modal('hide');
            $('#createInstructorModal').modal('hide');
        });
        
        Livewire.on('closeCreateModal', () => {
            $('#createInstructorModal').modal('hide');
        });
    </script>
@endpush
