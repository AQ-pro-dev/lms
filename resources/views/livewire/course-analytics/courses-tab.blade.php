<section class="popular-courses">
    <div class="popular-courses-inner px-5">
        <h3 class="mb-4 py-4">Your Courses</h3>
        <div class="card">
            <div class="card-body p-0">
                <table class="table table-borderless">
                    <thead>
                        <tr class="table-header">
                            <th>Course Name</th>
                            <th>Total Learners</th>
                            <th>Earnings</th>
                            <th>Class/Lecture Count</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($courses as $course)
                            {{-- @dd($course) --}}
                            <tr>
                                <td>
                                    <strong>{{ $course['course_name'] }}</strong>
                                </td>
                                <td class="text-center">{{ $course['total_learners'] }}</td>
                                <td class="text-center">${{ number_format($course['total_earnings'], 2) }}</td>
                                <td class="text-center">
                                    @if ($course['course_type'] == 'class_type')
                                        {{ $course['class_count'] }} Classes
                                    @elseif ($course['course_type'] == 'recorded')
                                        {{ $course['lecture_count'] }} Lectures
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($course['course_type'] == 'class_type')
                                        <a href="{{ route('course.details', $course['course_id']) }}"
                                            class="btn btn-outline-secondary btn-sm" wire:navigate>
                                            Details
                                        </a>
                                        {{-- {{ $course['class_count'] }} Classes --}}
                                    @elseif ($course['course_type'] == 'recorded')
                                        <a href="{{ route('onsite.course.details', $course['course_id']) }}"
                                            class="btn btn-outline-secondary btn-sm" wire:navigate>
                                            Details
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No courses found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
