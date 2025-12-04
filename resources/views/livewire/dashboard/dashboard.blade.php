<div class="dashboard py-5 px-4">
    @push('title')
        dashboard
    @endpush
    <section class="courses-info-section">
        <div class="inner">
            <h4 class="mb-4">Dashboard</h4>

            @if (Auth::user()->role_id == '2' || Auth::user()->role_id == '1')
                <div class="tutor-course-info d-flex align-items-center justify-content-center gap-3 flex-wrap">
                    <div class="course-box d-flex align-items-center justify-content-center flex-column mb-3">
                        <div class="course-box-icon">
                            <i class="fa-brands fa-google-scholar"></i>
                        </div>
                        <div class="number">
                            {{ $studentCount }}
                        </div>
                        <span>Total Student</span>
                    </div>
                    <div class="course-box d-flex align-items-center justify-content-center flex-column mb-3">
                        <div class="course-box-icon">
                            <i class="fa-solid fa-boxes-stacked"></i>
                        </div>
                        <div class="number">
                            {{ $totalCourses }}
                        </div>
                        <span>Total courses</span>
                    </div>
                </div>
            @endif
            <div class="courses-info">
                <div class="course-detail d-flex align-items-center justify-content-center gap-3 flex-wrap">
                    <div class="course-box d-flex align-items-center justify-content-center flex-column">
                        <div class="course-box-icon">
                            <i class="fa-solid fa-book-open-reader"></i>
                        </div>
                        <div class="number">
                            {{ $bookedCoursesCount }}
                        </div>
                        <span>Enrolled Courses</span>
                    </div>
                    <div class="course-box d-flex align-items-center justify-content-center flex-column">
                        <div class="course-box-icon">
                            <i class="fa-solid fa-graduation-cap"></i>
                        </div>
                        <div class="number">
                            {{ $inProgressCoursesCount }}
                        </div>
                        <span>Active Courses</span>
                    </div>
                    <div class="course-box d-flex align-items-center justify-content-center flex-column">
                        <div class="course-box-icon">
                            <i class="fa-solid fa-trophy">
                            </i>
                        </div>
                        <div class="number">
                            {{ $completedCoursesCount }}
                        </div>
                        <span>Completed Courses</span>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="course-progress py-2">
        <h4 class="my-5">In Progress Courses</h4>
        @if ($inProgressCourses->isEmpty())
            <div class="text-center py-5">
                <p>No courses are currently in progress.</p>
            </div>
        @else
            @foreach ($inProgressCourses as $progress)
                <div class="progress-wrapper py-2">
                    <div class="inner p-2">
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="progress-course-img">
                                    <img src="{{ $progress->course->thumbnail ?? asset('assets/images/progress-course-img.webp') }}"
                                        alt="">
                                </div>
                            </div>
                            <div class="col-lg-8">
                                <div class="progress-course-detail gap-1">
                                    <div class="progress-course-rating d-flex align-items-center my-3">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i class="fa-regular fa-star"
                                                style="color: {{ $i <= floor($progress->course->reviews->first()->average_rating ?? 0) ? '#eea015' : '#ddd' }};"></i>
                                        @endfor
                                        <span>
                                            {{ number_format($progress->course->reviews->first()->average_rating ?? 0, 2) }}
                                            <span>({{ $progress->course->reviews->first()->review_count ?? 0 }})</span>
                                        </span>
                                    </div>
                                    <h4>{{ $progress->course->title }}</h4>
                                    <div class="lessons-complete my-1">Completed <span>{{ $progress->progress }}% of
                                            100%</span></div>
                                    <div class="course-progress-bar d-flex align-items-center my-2 gap-1">
                                        <progress id="file" value="{{ $progress->progress }}"
                                            max="100">{{ $progress->progress }}%</progress>
                                        <p><span>{{ $progress->progress }}%</span> Complete</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </section>

    @if (Auth::user()->role_id == '2' || Auth::user()->role_id == '1')
        <section class="tutor-course-detail py-3 mt-3">
            <div class="inner">
                <h4 class="mb-4">My Courses</h4>
                @if ($courses->isEmpty())
                    <p class="text-center">No courses available at the moment.</p>
                @else
                    <div class="table-responsive">
                        <div class="card-table">
                            <table class="table table-striped align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th scope="col">Course Name</th>
                                        <th scope="col">Students Enrolled</th>
                                        <th scope="col">Highest Rating</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($courses as $course)
                                        <tr>
                                            <td>
                                                <a href="{{ route('course.details', $course->id) }}"
                                                    class="text-decoration-none">
                                                    {{ $course->title }}
                                                </a>
                                            </td>
                                            <td>{{ $course->enrolled_students ?? 0 }}</td>
                                            <td>
                                                @if ($course->reviews->isNotEmpty())
                                                    @php
                                                        $highestRating = $course->reviews->first()->highest_rating ?? 0;
                                                    @endphp
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        <i class="fa-regular fa-star"
                                                            style="color: {{ $i <= $highestRating ? '#eea015' : '#ddd' }};"></i>
                                                    @endfor
                                                    <span>({{ number_format($highestRating, 1) }})</span>
                                                @else
                                                    <span>No Ratings</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        </section>
    @endif

</div>
