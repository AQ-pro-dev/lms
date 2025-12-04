<div class="CourseRequest py-5 px-4">
    @push('title')
        Course Requests
    @endpush
    <h2 class="mb-4">Course Requests</h2>
    <section class="tag-list mt-5">
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Course Title</th>
                        <th>Description</th>
                        <th>Created By</th>
                        <th>Approve</th>
                        <th>Reject</th>
                        <th>View</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($courses as $course)
                        <tr>
                            <td>{{ $course->id }}</td>
                            <td>{{ $course->title }}</td>
                            <td>{{ Str::limit($course->description, 50) }}</td>
                            <td>{{ $course->user->first_name }} {{ $course->user->last_name }}</td>
                            <td>
                                <button class="btn btn-outline-success" wire:click="approveCourse({{ $course->id }})"
                                    wire:loading.attr="disabled" wire:target="approveCourse({{ $course->id }})"
                                    @if ($course->is_published) disabled @endif>
                                    <span wire:loading.remove
                                        wire:target="approveCourse({{ $course->id }})">Approve</span>
                                    <span wire:loading wire:target="approveCourse({{ $course->id }})">
                                        <span class="spinner-border spinner-border-sm" role="status"
                                            aria-hidden="true"></span>
                                    </span>
                                </button>
                            </td>
                            <td>
                                <button class="btn btn-outline-danger" wire:click="rejectCourse({{ $course->id }})"
                                    wire:loading.attr="disabled" wire:target="rejectCourse({{ $course->id }})"
                                    @if ($course->is_published) disabled @endif>
                                    <span wire:loading.remove
                                        wire:target="rejectCourse({{ $course->id }})">Reject</span>
                                    <span wire:loading wire:target="rejectCourse({{ $course->id }})">
                                        <span class="spinner-border spinner-border-sm" role="status"
                                            aria-hidden="true"></span>
                                    </span>
                                </button>
                            </td>
                            <td>
                                <a href="{{ route('course.details', $course->id) }}" target="_blank"
                                    class="btn btn-outline-warning">View Course</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">
                                No courses available at the moment.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </section>
</div>
