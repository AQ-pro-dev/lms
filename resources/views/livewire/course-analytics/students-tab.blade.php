<div class="students-tab px-5">
    <div wire:ignore class="filters d-flex flex-row flex-md-row gap-3 align-items-start mb-4">
        <div class="flex-grow-1">
            <label for="">Search for Students</label>
            <input type="text" class="form-control search-input" placeholder="Search..." wire:model.live="search" />
        </div>
        <div class="flex-grow-1">
            <label for="courses" class="form-label">Courses</label>
            <select class="form-control" wire:model="selectedCourse" wire:change="fetchStudents">
                <option value="">All Courses</option>
                @foreach ($courses as $course)
                    <option value="{{ $course->id }}">{{ $course->title }}</option>
                @endforeach
            </select>
        </div>
        <div class="date-input flex-grow-1">
            <label for="date" class="form-label">Date</label>
            <input type="date" class="form-control" id="calendar-input-students-tab" placeholder="Y-M-d"
                wire:model="selectedDate" wire:change="fetchStudents" />
        </div>
    </div>

    <div class="table-responsive">
        <table class="table students-table">
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Registration Date</th>
                    <th>Course Taken</th>
                    <th>Progress</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($students as $student)
                    <tr>
                        <td>
                            <div class="student-info d-flex align-items-center">
                                <img src="{{ $student['user']->pfp ? asset('storage/' . $student['user']->pfp) : asset('assets/images/dummy-profile-photo.webp') }}"
                                    alt="Profile Picture" class="profile-pic me-3">
                                <div>
                                    <strong>{{ $student['user']->first_name . ' ' . $student['user']->last_name }}</strong><br>
                                    <small class="text-muted">{{ $student['user']->email }}</small>
                                </div>
                            </div>
                        </td>
                        <td class="text-center">{{ $student['registration_date']->format('Y-m-d') }}</td>
                        <td class="text-center">{{ $student['course']->title }}</td>
                        <td>
                            @if ($student['course']->course_type === 'class_type')
                                <span>No progress due to class type</span>
                            @else
                                <div>
                                    <label for="progress-{{ $student['course']->id }}" class="d-block">
                                        Course Progress: {{ $student['progress'] }}%
                                    </label>
                                    <progress id="progress-{{ $student['course']->id }}"
                                        value="{{ $student['progress'] }}" max="100" class="w-100">
                                        {{ $student['progress'] }}%
                                    </progress>
                                </div>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@script
    <script>
        flatpickr("#calendar-input-students-tab", {
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "F j, Y",
            defaultDate: new Date(),
        });
    </script>
@endscript
