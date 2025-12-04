<div class="students">
    <div class="mt-4">
        <h4 class="mb-3 text-primary fw-bold">Students and Enrolled Courses</h4>

        <div class="table-wrapper">
            <div class="table-responsive ">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Student Name</th>
                            <th scope="col">Email</th>
                            <th scope="col">Enrolled Courses</th>
                            <th scope="col">Block/Unblock</th>
                            <th scope="col">Promote</th>
                            <th scope="col">Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($studentsWithEnrollments as $index => $data)
                            @php $user = $data['student']; @endphp
                            <tr class="{{ $user->trashed() ? 'table-danger' : '' }}">
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $data['count'] }}</td>
                                {{-- Block/Unblock Dropdown --}}
                                <td>
                                    <div class="dropdown">
                                        <button
                                            class="btn btn-sm {{ $user->trashed() ? 'btn-outline-success' : 'btn-outline-danger' }} dropdown-toggle"
                                            type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            {{ $user->trashed() ? 'Unblock' : 'Block' }}
                                        </button>
                                        <ul class="dropdown-menu shadow-sm">
                                            @if ($user->trashed())
                                                <li>
                                                    <a href="#" class="dropdown-item text-success fw-semibold"
                                                        wire:click.prevent="unblockUser({{ $user->id }})">
                                                        <i class="bi bi-unlock me-2"></i> Unblock User
                                                    </a>
                                                </li>
                                            @else
                                                <li>
                                                    <a href="#" class="dropdown-item text-danger fw-semibold"
                                                        wire:click.prevent="blockUser({{ $user->id }})">
                                                        <i class="bi bi-lock me-2"></i> Block User
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
                                            @if ($user->role_id !== 2)
                                                <li>
                                                    <a href="#" class="dropdown-item text-primary fw-semibold"
                                                        wire:click.prevent="promoteUser({{ $user->id }}, 2)">
                                                        <i class="bi bi-person-gear me-2"></i> Instructor
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
                                        aria-label="View student details">
                                        Details
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-3">
                                    <i class="bi bi-emoji-frown me-2"></i> No students found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    {{-- student modal --}}
    <div wire:ignore.self class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false"
        tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5 text-white">
                        <i class="bi bi-person-lines-fill me-2 text-white"></i> Student Details
                    </h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="updateStudent">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label>First Name</label>
                                <input type="text" class="form-control"
                                    wire:model.defer="selectedStudentData.first_name">
                            </div>
                            <div class="col-md-6">
                                <label>Last Name</label>
                                <input type="text" class="form-control"
                                    wire:model.defer="selectedStudentData.last_name">
                            </div>
                            <div class="col-md-6">
                                <label>Username</label>
                                <input type="text" class="form-control"
                                    wire:model.defer="selectedStudentData.username">
                            </div>
                            <div class="col-md-6">
                                <label>Email</label>
                                <input type="email" class="form-control" wire:model.defer="selectedStudentData.email">
                            </div>
                            <div class="col-md-6">
                                <label>Phone</label>
                                <input type="text" class="form-control" wire:model.defer="selectedStudentData.phone">
                            </div>
                            <div class="col-md-6">
                                <label>Timezone</label>
                                <input type="text" class="form-control"
                                    wire:model.defer="selectedStudentData.timezone">
                            </div>
                            <div class="col-md-6">
                                <label>New Password <small class="text-muted">(leave blank to keep
                                        current)</small></label>
                                <input type="password" class="form-control" wire:model.defer="newPassword">
                            </div>
                            <div class="col-12">
                                <label>Bio</label>
                                <textarea class="form-control" rows="2" wire:model.defer="selectedStudentData.bio"></textarea>
                            </div>
                            <div class="col-md-6">
                                <label>Microsoft Account</label><br>
                                @if (data_get($selectedStudentData, 'microsoft_account'))
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
                                    wire:model.defer="selectedStudentData.facebook">
                            </div>
                            <div class="col-md-6">
                                <label>Twitter</label>
                                <input type="text" class="form-control"
                                    wire:model.defer="selectedStudentData.twitter">
                            </div>
                            <div class="col-md-6">
                                <label>LinkedIn</label>
                                <input type="text" class="form-control"
                                    wire:model.defer="selectedStudentData.linkedin">
                            </div>
                            <div class="col-md-6">
                                <label>Website</label>
                                <input type="text" class="form-control"
                                    wire:model.defer="selectedStudentData.website">
                            </div>
                            <div class="col-md-6">
                                <label>GitHub</label>
                                <input type="text" class="form-control"
                                    wire:model.defer="selectedStudentData.github">
                            </div>
                            <div class="col-12 mt-4">
                                <h6 class="text-primary fw-bold">Enrolled Courses</h6>
                                <ul class="list-group">
                                    @forelse($selectedStudentCourses as $course)
                                        <li class="list-group-item">{{ $course->title }}</li>
                                    @empty
                                        <li class="list-group-item text-muted">No enrolled courses.</li>
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
</div>
@push('js')
    <script>
        window.addEventListener('close-modal', event => {
            $('#staticBackdrop').modal('hide');
        });
    </script>
@endpush
