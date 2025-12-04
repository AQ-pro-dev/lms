<div class="announcements py-5 px-4">
    @push('title')
        Announcements
    @endpush
    <!-- Announcement Card Section -->
    <section class="announcement-card p-3 mb-4">
        <div class="d-flex flex-wrap align-content-center justify-content-center">
            <div class="col-md-7">
                <ul class="d-flex align-items-center gap-3 w-100 flex-wrap">
                    <li>
                        <div class="announcement-icon d-flex align-items-center justify-content-center">
                            <i class="fa-solid fa-bullhorn"></i>
                        </div>
                    </li>
                    <li>
                        <div class="announcement-text">
                            <span>Create Announcement</span>
                            <p>Notify all students of your course</p>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="col-md-5 d-flex justify-content-center align-items-center">
                <!-- Trigger Modal Button -->
                <button class="button-primary" data-bs-toggle="modal" data-bs-target="#createAnnouncementModal">
                    Add New Announcement
                </button>
            </div>
        </div>
    </section>
    <section class="filter-courses">
        <div wire:ignore class="row">
            <!-- Courses Select -->
            <div class="col-md-6">
                <label for="courseFilter" class="form-label">Courses</label>
                <select class="form-select" id="courseFilter" wire:model="courseFilter"
                    wire:change="filterAnnouncements">
                    <option value="">All</option>
                    @foreach ($courses as $course)
                        <option value="{{ $course->id }}">{{ $course->title }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Sort Order Select -->
            <div class="col-md-3">
                <label for="sortOrderFilter" class="form-label">Sort Order</label>
                <select class="form-select" id="sortOrderFilter" wire:model="sortOrderFilter"
                    wire:change="filterAnnouncements">
                    <option value="DESC">DESC</option>
                    <option value="ASC">ASC</option>
                </select>
            </div>

            <!-- Date Input -->
            <div class="col-md-3">
                <label for="dateFilter" class="form-label">Date</label>
                <input type="date" class="form-control" wire:model="dateFilter" id="calendar-input-students-tab"
                    wire:change="filterAnnouncements" />
            </div>
        </div>
    </section>

    <!-- Announcements Table -->
    <div class="announcement-table mt-5">
        <div class="card-table">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Date</th>
                        <th scope="col">Announcement</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($announcements->isEmpty())
                        <!-- No Announcements -->
                        <tr>
                            <td colspan="2" class="text-center">
                                <img src="{{ asset('assets/images/announcment.webp') }}" alt="No Announcements" />
                            </td>
                        </tr>
                    @else
                        @foreach ($announcements as $announcement)
                            <tr>
                                <td>{{ $announcement->created_at->format('d M Y') }}</td>
                                <td>{{ $announcement->subject }}</td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal for Creating Announcement -->
    <div wire:ignore.self class="modal fade" id="createAnnouncementModal" tabindex="-1"
        aria-labelledby="createAnnouncementModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form wire:submit.prevent="createAnnouncement">
                    <div class="modal-header">
                        <h5 class="modal-title text-white" id="createAnnouncementModalLabel">Create Announcement</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Course Selection -->
                        <div class="mb-3">
                            <label for="course" class="form-label">Select Course</label>
                            <select wire:model="selectedCourse" id="course" class="form-select">
                                <option value="">-- Select a Course --</option>
                                @foreach ($courses as $course)
                                    <option value="{{ $course->id }}">{{ $course->title }}</option>
                                @endforeach
                            </select>
                            @error('selectedCourse')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Subject Field -->
                        <div class="mb-3">
                            <label for="subject" class="form-label">Subject</label>
                            <input type="text" wire:model="subject" id="subject" class="form-control">
                            @error('subject')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Message Field -->
                        <div class="mb-3">
                            <label for="message" class="form-label">Message</label>
                            <textarea wire:model="message" id="message" rows="4" class="form-control"></textarea>
                            @error('message')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="button-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="button-primary">Send Announcement</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@script
    <script>
        // Initialize Flatpickr
        flatpickr("#calendar-input-students-tab", {
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "F j, Y",
        });
    </script>
@endscript
@push('js')
    <script>
        window.addEventListener('closeModal', event => {
            $('#createAnnouncementModal').modal('hide');
        });
    </script>
@endpush
