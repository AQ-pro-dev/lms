@php
    $user = auth()->user();
    $tutorRequest = $user->tutor ?? null;
    $isTutorVerified = $tutorRequest ? $user->tutor->is_verified : false;
    $isAdmin = $user->role_id == 1;
@endphp

<div class="dashboard-head mt-5">
    <div class="container">
        <div class="inner d-flex align-items-center justify-content-between tutor-data flex-wrap pb-2">
            <!-- User Information -->
            <div class="tutor-info">
                <ul class="tutor-avatar d-flex align-items-center gap-3">
                    <li>
                        <img
                            src="{{ $user->pfp ? asset('storage/' . $user->pfp) : asset('assets/images/dummy-profile-photo.webp') }}">
                    </li>
                    <li>
                        <span>Hello,</span>
                        <b class="d-block">{{ $username }}</b>
                    </li>
                </ul>
            </div>

            <!-- Notifications and Actions -->
            <div class="tutor-notification-request d-flex gap-3 align-items-center">
                <!-- Notifications Button -->
                <button class="offcanvas-btn position-relative btn btn-light" type="button" data-bs-toggle="offcanvas"
                    data-bs-target="#offcanvasRight" aria-controls="offcanvasRight">
                    <i class="fa-solid fa-bell"></i>
                    @if ($unreadCount > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            {{ $unreadCount }}
                        </span>
                    @endif
                </button>


                @if ($isAdmin || ($tutorRequest && $isTutorVerified))
                    <button type="button" class="button-secondary"
                        onclick="location.href='{{ route('dashboard.create.course') }}'">
                        Create Course
                    </button>
                @elseif ($tutorRequest && !$isTutorVerified)
                    <p class="text-danger mb-0">Your request to become a tutor is pending.</p>
                @else
                    <button type="button" class="button-secondary" data-bs-toggle="modal"
                        data-bs-target="#instructorModal">
                        Become an Instructor
                    </button>
                @endif

            </div>
        </div>

        <!-- Notifications Sidebar -->
        <div wire:ignore.self class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight"
            aria-labelledby="offcanvasRightLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="offcanvasRightLabel">Notifications</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                    aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <div class="notification-request ">
                    @if ($notifications->isEmpty())
                        <p>No notifications available.</p>
                    @else
                        @foreach ($notifications as $notification)
                            <div class="notification-item d-flex justify-content-between align-items-center"
                                wire:click="markAsRead({{ $notification->id }})">
                                <div>
                                    <p class="mb-0">{{ $notification->message }}</p>
                                    <span
                                        class="text-muted small">{{ $notification->created_at->diffForHumans() }}</span>
                                </div>
                                @if ($notification->read_status)
                                    <span class="badge bg-success">Read</span>
                                @endif
                            </div>
                            <hr>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Become an Instructor Modal -->
    <div wire:ignore.self class="modal fade" id="instructorModal" tabindex="-1" aria-labelledby="instructorModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-white" id="instructorModalLabel">Become an Instructor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="createTutor">
                        <div class="mb-3">
                            <label for="specialization" class="form-label">Specialization</label>
                            <input type="text" wire:model="specialization" class="form-control" id="specialization"
                                placeholder="Enter your specialization">
                            @error('specialization')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="preferred_teaching_method" class="form-label">Preferred Teaching Method</label>
                            <select wire:model="preferred_teaching_method" class="form-select">
                                <option value="">Select</option>
                                <option value="online">Online</option>
                                <option value="in-person">In-Person</option>
                            </select>
                            @error('preferred_teaching_method')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="experience" class="form-label">Experience</label>
                            <textarea wire:model="experience" class="form-control" id="experience" placeholder="Describe your experience"></textarea>
                            @error('experience')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="file_path" class="form-label">Attach Your CV</label>
                            <input type="file" wire:model="file_path" class="form-control">

                            <!-- Show "Uploading..." when the file is being uploaded -->
                            <div wire:loading wire:target="file_path">
                                <small class="text-muted">Uploading...</small>
                            </div>

                            @error('file_path')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Disable submit button while uploading -->
                        <button type="submit" class="button-primary w-100" wire:loading.attr="disabled"
                            wire:target="file_path">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@push('js')
    <script>
        window.addEventListener('close-modal', event => {
            $('#instructorModal').modal('hide');
        });
    </script>
@endpush
