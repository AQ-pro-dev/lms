<div class="my-course py-5 px-4 w-100">
    @push('title')
        My Courses
    @endpush
    <h2 class="mb-4">My Courses</h2>

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
                        </div>
                    </div>
                @endforeach
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
        });
    </script>
@endpush
