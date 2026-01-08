<div class="my-course py-5 px-4 w-100">
    @push('title')
        @if(auth()->check() && auth()->user()->role_id == 1) Courses @else My Courses @endif
    @endpush
    <h2 class="mb-4">@if(auth()->check() && auth()->user()->role_id == 1) Courses @else My Courses @endif</h2>

    <!-- Nav Tabs -->
    <ul class="nav nav-tabs" id="courseTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link {{ $activeTab === 'published' ? 'active' : '' }}"
                wire:click.prevent="setActiveTab('published')" id="published-tab" data-bs-toggle="tab" href="#published"
                role="tab">
                Published ({{ $publishedCourses->count() }})
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $activeTab === 'pending' ? 'active' : '' }}"
                wire:click.prevent="setActiveTab('pending')" id="pending-tab" data-bs-toggle="tab" href="#pending"
                role="tab">
                Pending ({{ $pendingCourses->count() }})
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $activeTab === 'drafted' ? 'active' : '' }}"
                wire:click.prevent="setActiveTab('drafted')" id="drafted-tab" data-bs-toggle="tab" href="#drafted"
                role="tab">
                Draft ({{ $draftedCourses->count() }})
            </a>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content mt-4" id="courseTabsContent">
        @if(auth()->user()->role_id == 1)
            {{-- ADMIN VIEW: TABLE LAYOUT --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Course Name</th>
                            <th>Created By</th>
                            <th>Enrolled Students</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $courses = match($activeTab) {
                                'published' => $publishedCourses,
                                'pending' => $pendingCourses,
                                'drafted' => $draftedCourses,
                                default => collect([]),
                            };
                        @endphp

                        @foreach($courses as $course)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($course->thumbnail)
                                            <img src="{{ asset('storage/' . $course->thumbnail) }}" 
                                                 alt="Thumbnail" 
                                                 class="rounded me-3" 
                                                 style="width: 50px; height: 50px; object-fit: cover;">
                                        @else
                                             <div class="rounded me-3 bg-secondary" style="width: 50px; height: 50px;"></div>
                                        @endif
                                        <div>
                                            <h6 class="mb-0">{{ $course->title }}</h6>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    {{ $course->user->first_name }} {{ $course->user->last_name }}
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-info" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#viewStudentsModal"
                                            wire:click.prevent="viewStudents({{ $course->id }})">
                                        <i class="fa-solid fa-users"></i> {{ $course->bookings_count ?? 0 }} Students
                                    </button>
                                </td>
                                <td>{{ $course->price > 0 ? '$'.number_format($course->price, 2) : 'Free' }}</td>
                                <td>
                                    @if($activeTab == 'published')
                                        <span class="badge bg-success">Published</span>
                                    @elseif($activeTab == 'pending')
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @else
                                        <span class="badge bg-secondary">Draft</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('dashboard.create.course', ['courseId' => $course->id]) }}" class="btn btn-sm btn-outline-primary me-2">
                                        <i class="fa-regular fa-pen-to-square"></i>
                                    </a>
                                    <button class="btn btn-sm btn-outline-danger" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#deleteCourseModal"
                                            wire:click.prevent="confirmDelete({{ $course->id }})">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <!-- Published Tab -->
            <div class="tab-pane fade {{ $activeTab === 'published' ? 'show active' : '' }}" id="published" role="tabpanel">
                <div class="row">
                    @foreach ($publishedCourses as $publishedCourse)
                        <div class="col-12 col-md-6 col-xl-4 mb-4">
                            <div class="card shadow">
                                <div class="card-img-top">
                                    <img src="{{ asset('storage/' . $publishedCourse->thumbnail) }}" alt="Course"
                                        class="img-fluid">
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title my-3">{{ $publishedCourse->title }}</h5>
                                </div>
                                <div class="card-footer d-flex justify-content-between align-items-center">
                                    <p>Price: <span>{{ $publishedCourse->price ?? 'Free' }}</span></p>
                                    <p>
                                        <a href="{{ route('dashboard.create.course', ['courseId' => $publishedCourse->id]) }}"
                                            class="edit-link">
                                            <i class="fa-regular fa-pen-to-square"></i>
                                        </a>
                                        <a href="#" class="delete-link" data-bs-toggle="modal"
                                            data-bs-target="#deleteCourseModal"
                                            wire:click.prevent="confirmDelete({{ $publishedCourse->id }})">
                                            <i class="fa-regular fa-trash-can"></i>
                                        </a>
                                    </p>
                                </div>
                                @if ($publishedCourse->course_type == 'recorded')
                                    <a class="button-primary w-100 text-center"
                                        href="{{ route('dashboard.view.lectures', ['courseId' => $publishedCourse->id]) }}">
                                        View Lectures
                                    </a>
                                @endif

                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Pending Tab -->
            <div class="tab-pane fade {{ $activeTab === 'pending' ? 'show active' : '' }}" id="pending" role="tabpanel">
                <div class="row">
                    @foreach ($pendingCourses as $pendingCourse)
                        <div class="col-12 col-md-6 col-lg-4 mb-4">
                            <div class="card shadow">
                                <div class="card-img-top">
                                    <img src="{{ asset('storage/' . $pendingCourse->thumbnail) }}" alt="Course"
                                        class="img-fluid">
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title my-3">{{ $pendingCourse->title }}</h5>
                                </div>
                                <div class="card-footer d-flex justify-content-between align-items-center">
                                    <p>Price: <span>{{ $pendingCourse->price ?? 'Free' }}</span></p>
                                    <p>
                                        <a href="{{ route('dashboard.create.course', ['courseId' => $pendingCourse->id]) }}"
                                            class="edit-link">
                                            <i class="fa-regular fa-pen-to-square"></i>
                                        </a>
                                        <a href="#" class="delete-link" data-bs-toggle="modal"
                                            data-bs-target="#deleteCourseModal"
                                            wire:click.prevent="confirmDelete({{ $pendingCourse->id }})">
                                            <i class="fa-regular fa-trash-can"></i>
                                        </a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Drafted Tab -->
            <div class="tab-pane fade {{ $activeTab === 'drafted' ? 'show active' : '' }}" id="drafted" role="tabpanel">
                <div class="row">
                    @foreach ($draftedCourses as $draftedCourse)
                        <div class="col-12 col-md-6 col-lg-4 mb-4">
                            <div class="card shadow">
                                <div class="card-img-top">
                                    <img src="{{ asset('storage/' . $draftedCourse->thumbnail) }}" alt="Course"
                                        class="img-fluid">
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title my-3">{{ $draftedCourse->title }}</h5>
                                </div>
                                <div class="card-footer d-flex justify-content-between align-items-center">
                                    <p>Price: <span>{{ $draftedCourse->price ?? 'Free' }}</span></p>
                                    <p>
                                        <a href="{{ route('dashboard.create.course', ['courseId' => $draftedCourse->id]) }}"
                                            class="edit-link">
                                            <i class="fa-regular fa-pen-to-square"></i>
                                        </a>
                                        <a href="#" class="delete-link" data-bs-toggle="modal"
                                            data-bs-target="#deleteCourseModal"
                                            wire:click.prevent="confirmDelete({{ $draftedCourse->id }})">
                                            <i class="fa-regular fa-trash-can"></i>
                                        </a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <!-- Enrolled Students Modal -->
    <div wire:ignore.self class="modal fade" id="viewStudentsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Enrolled Students for {{ $selectedCourseTitle }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @if(count($enrolledStudents) > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        {{-- <th>Joined At</th> --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($enrolledStudents as $student)
                                        <tr>
                                            <td>
                                                <a href="{{ route('dashboard.student.view', ['id' => $student->id]) }}" 
                                                   class="text-decoration-none fw-bold text-primary">
                                                    {{ $student->first_name }} {{ $student->last_name }}
                                                </a>
                                            </td>
                                            <td>{{ $student->email }}</td>
                                            {{-- <td>{{ $student->created_at->format('d M, Y') }}</td> --}}
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-center">No students enrolled yet.</p>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div wire:ignore.self class="modal fade" id="deleteCourseModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-white">Delete Course</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form wire:submit.prevent="destroyCourse">
                    <div class="modal-body">
                        Are you sure you want to delete this course?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger">Yes, Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('js')
    <script>
        window.addEventListener('close-modal', event => {
            $('#deleteCourseModal').modal('hide');
            // Check if viewStudentsModal is open and close it if necessary
            // $('#viewStudentsModal').modal('hide'); 
        });
    </script>
@endpush
