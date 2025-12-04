<div class="course-section py-5 px-4">
    @push('title')
        Enrolled Courses
    @endpush
    <h2 class="mb-4">Enrolled Courses</h2>
    <!-- Nav Tabs -->
    <ul class="nav nav-tabs" id="courseTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="enrolled-tab" data-bs-toggle="tab" href="#enrolled" role="tab"
                aria-controls="enrolled" aria-selected="true">
                Enrolled Courses ({{ count($enrolledCourses) }})
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="active-tab" data-bs-toggle="tab" href="#active" role="tab"
                aria-controls="active" aria-selected="false">
                Active Courses ({{ count($activeCourses) }})
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="completed-tab" data-bs-toggle="tab" href="#completed" role="tab"
                aria-controls="completed" aria-selected="false">
                Completed Courses ({{ count($completedCourses) }})
            </a>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content mt-4" id="courseTabsContent">
        <!-- Enrolled Courses Tab -->
        <div class="tab-pane fade show active" id="enrolled" role="tabpanel" aria-labelledby="enrolled-tab">
            <div class="enrolled-cards d-flex flex-wrap gap-4">
                @foreach ($enrolledCourses as $course)
                    <div class="card shadow">
                        <div class="card-img-top">
                            <img src="{{ asset('storage/' . $course['course']->thumbnail) }}"
                                alt="{{ $course['course']->title }}">
                        </div>
                        <div class="card-body">
                            <h5 class="card-title my-3">{{ $course['course']->title }}</h5>
                            <p>{{ $course['course']->description }}</p>

                            <!-- Progress Bar with percentage inside -->
                            <p class="d-flex justify-content-end">Compelete {{ $course['progress'] }}%</p>
                            <div class="progress">
                                <div class="progress-bar" role="progressbar" style="width: {{ $course['progress'] }}%"
                                    aria-valuenow="{{ $course['progress'] }}" aria-valuemin="0" aria-valuemax="100">
                                    {{-- {{ $course['progress'] }}% --}}
                                </div>
                            </div>

                            <div class="text-center">
                                <a href="#" class="button-primary mt-3">Start Learning</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Active Courses Tab -->
        <div class="tab-pane fade" id="active" role="tabpanel" aria-labelledby="active-tab">
            <div class="active-cards d-flex flex-wrap gap-4">
                @foreach ($activeCourses as $course)
                    <div class="card shadow">
                        <div class="card-img-top">
                            <img src="{{ asset('storage/' . $course['course']->thumbnail) }}"
                                alt="{{ $course['course']->title }}">
                        </div>
                        <div class="card-body">
                            <h5 class="card-title my-3">{{ $course['course']->title }}</h5>
                            <p>{{ $course['course']->description }}</p>

                            <!-- Progress Bar with percentage inside -->
                            <p class="d-flex justify-content-end">Compelete {{ $course['progress'] }}%</p>
                            <div class="progress">
                                <div class="progress-bar" role="progressbar" style="width: {{ $course['progress'] }}%"
                                    aria-valuenow="{{ $course['progress'] }}" aria-valuemin="0" aria-valuemax="100">
                                </div>
                            </div>
                            <div class="text-center">
                                <a href="#" class="button-primary mt-3">Continue Learning</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Completed Courses Tab -->
        <div class="tab-pane fade" id="completed" role="tabpanel" aria-labelledby="completed-tab">
            <div class="completed-cards d-flex flex-wrap gap-4">
                @foreach ($completedCourses as $course)
                    <div class="card shadow">
                        <div class="card-img-top">
                            <img src="{{ asset('storage/' . $course['course']->thumbnail) }}"
                                alt="{{ $course['course']->title }}">
                        </div>
                        <div class="card-body">
                            <h5 class="card-title my-3">{{ $course['course']->title }}</h5>
                            <p>{{ $course['course']->description }}</p>

                            <!-- Progress Bar with percentage inside -->
                            <p class="d-flex justify-content-end">Compelete {{ $course['progress'] }}%</p>
                            <div class="progress">
                                <div class="progress-bar" role="progressbar" style="width: {{ $course['progress'] }}%"
                                    aria-valuenow="{{ $course['progress'] }}" aria-valuemin="0" aria-valuemax="100">
                                    {{-- {{ $course['progress'] }}% --}}
                                </div>
                            </div>

                            <div class="text-center">
                                <a href="#" class="button-primary mt-3">View Certificate</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
