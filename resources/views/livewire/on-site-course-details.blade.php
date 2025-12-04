<div class="onsite-course-page">
    <section class="onsite-course-header">
        <div class="container">
            <div class="inner">
                <h3>Course Description</h3>
                <h1 class="onsite-course-title mb-5">{{ $course->title }}</h1>
                <p class="onsite-course-subdescription">
                    This course offers an introduction into personality disorders. There is discussion around each
                    personality disorder cluster,
                    how their characteristics can present in an escalated situation, and suggest strategies for
                    de-escalation.
                    This course also offers a 1 CEU credit for individuals licensed with the NC LCAS board.
                </p>
            </div>
        </div>
    </section>

    <section class="onsite-course-info">
        <div class="container">
            <div class="inner row">
                <div class="col-md-6 onsite-course-details">
                    <div class="onsite-course-pricing">
                        @if ($price == 0)
                            <span class="onsite-price">FREE</span>
                        @else
                            <span class="onsite-price">{{ $price }}$</span>
                        @endif
                        @if ($isEnrolled)
                            <!-- Show 'Enrolled' Button -->
                            <button type="button" class="btn btn-success">Enrolled</button>
                        @else
                            <!-- Show 'Enroll NOW' Button -->
                            <button type="button" class="onsite-add-to-cart button-primary" data-bs-toggle="modal"
                                data-bs-target="#courseEnrollment">
                                Enroll NOW
                            </button>
                        @endif
                    </div>
                    <div class="onsite-enrollment">
                        <span class="onsite-validity my-2"><i class="bi bi-calendar4"></i> Enrollment
                            validity:
                            Lifetime</span>
                    </div>
                    <div class="onsite-subject">
                        <span>Subject</span> </span>Understanding Personality Disorders</span>
                    </div>
                </div>

                <div class="col-md-6 onsite-course-meta">
                    <ul class="onsite-meta-list">
                        {{-- <li><i class="bi bi-bar-chart"></i> All Levels</li> --}}
                        <li><i class="bi bi-person"></i> {{ $course->bookings->count() }}</li>
                        <li><i class="bi bi-clock"></i> {{ gmdate('H:i:s', $course->lectures->sum('video_duration')) }}
                            Duration</li>
                        <li><i class="bi bi-arrow-clockwise"></i> {{ $course->updated_at }} Last Updated</li>
                        <li><i class="bi bi-award"></i> Certificate of completion</li>
                    </ul>

                </div>
            </div>
        </div>
    </section>

    <section class="onsite-course-overview">
        <div class="container">
            <div class="inner">
                <div class="course-details-tabs mt-5">
                    <!-- Tab Navigation -->
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" id="most-popular-tab" data-bs-toggle="tab" href="#most-popular"
                                role="tab" aria-controls="most-popular" aria-selected="true">Course Info</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="new-tab" data-bs-toggle="tab" href="#new" role="tab"
                                aria-controls="new" aria-selected="false" tabindex="-1">Reviews</a>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active text-start" id="most-popular" role="tabpanel"
                            aria-labelledby="most-popular-tab">
                            <div class="course-details__content">
                                <h3 class="mt-3">About Course</h3>

                                <!-- Content Wrapper -->
                                <div class="content-wrapper {{ $showFullDescription ? 'expanded' : '' }}">
                                    <h3>What Will You Learn?</h3>
                                    <ul>
                                        <li>{{ $learningOutcomes }}</li>
                                    </ul>

                                    @if ($showFullDescription)
                                        <h3>Target Audience</h3>
                                        <ul>
                                            <li>{{ $targetAudience }}</li>
                                        </ul>
                                        <h3>Requirements</h3>
                                        <ul>
                                            <li>{{ $requirements }}</li>
                                        </ul>
                                    @endif
                                </div>

                                <!-- Toggle Button -->
                                <button wire:click="toggleDescription" class="showmore-btn my-3">
                                    {!! $showFullDescription
                                        ? '<i class="fa-solid fa-minus"></i> Show Less'
                                        : '<i class="fa-solid fa-plus"></i> Show More' !!}
                                </button>
                                <div class="accordion" id="accordionPanelsStayOpenExample">
                                    @foreach ($lectures as $index => $lecture)
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button {{ $index === 0 ? '' : 'collapsed' }}"
                                                    type="button" data-bs-toggle="collapse"
                                                    data-bs-target="#panelsStayOpen-collapse{{ $index }}"
                                                    aria-expanded="{{ $index === 0 ? 'true' : 'false' }}"
                                                    aria-controls="panelsStayOpen-collapse{{ $index }}">
                                                    {{ $lecture->title }} <i
                                                        class="fa-solid fa-circle-exclamation ms-4"></i>
                                                </button>
                                            </h2>
                                            <div id="panelsStayOpen-collapse{{ $index }}"
                                                class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}">
                                                @if ($isEnrolled)
                                                    <div
                                                        class="accordion-body d-flex align-items-center justify-content-between">
                                                        <div class="class-name d-flex align-items-center">
                                                            <i class="fa-brands fa-youtube mx-2"></i>
                                                            <a
                                                                href="{{ route('recorded.lecture', ['courseId' => $courseId, 'lectureId' => $lecture->id]) }}">
                                                                <span>{{ $lecture->title }}</span>
                                                            </a>
                                                        </div>
                                                        <div>
                                                            <span class="class-duration">Duration:</span>
                                                            {{ $lecture->duration }} <i class="fa-solid fa-unlock"></i>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="accordion-body d-flex align-items-center justify-content-between text-muted opacity-50"
                                                        style="pointer-events: none;">
                                                        <div class="class-name d-flex align-items-center">
                                                            <i class="fa-brands fa-youtube mx-2"></i>
                                                            <a href="#" onclick="return false;"
                                                                style="text-decoration: none; color: inherit;">
                                                                <span>{{ $lecture->title }}</span>
                                                            </a>
                                                        </div>
                                                        <div>
                                                            <span class="class-duration">Duration:</span>
                                                            {{ $lecture->duration }} <i class="fa-solid fa-lock"></i>
                                                        </div>
                                                    </div>
                                                @endif

                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                            </div>

                        </div>

                        <div class="tab-pane fade" id="new" role="tabpanel" aria-labelledby="new-tab">
                            <div class="course-details__widget">
                                <h3 class="text-left d-flex my-4 fs-5">Student Ratings & Reviews</h3>

                                @if ($course->reviews->isNotEmpty())
                                    <div class="reviews-section">
                                        @foreach ($course->reviews as $review)
                                            <div class="card mb-3 shadow-sm">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <h5 class="mb-0">{{ $review->user->first_name }}
                                                            {{ $review->user->last_name }}</h5>
                                                        <div class="rating">
                                                            @for ($i = 1; $i <= 5; $i++)
                                                                <i
                                                                    class="bi {{ $i <= $review->rating ? 'bi-star-fill text-warning' : 'bi-star text-muted' }}"></i>
                                                            @endfor
                                                        </div>
                                                    </div>
                                                    <p class="mt-2 mb-1 text-secondary">{{ $review->review }}</p>
                                                    <small
                                                        class="text-muted">{{ $review->created_at->format('F j, Y, g:i a') }}</small>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="d-flex flex-column align-items-center">
                                        <img src="{{ asset('assets/svgs/emptystate.svg') }}" alt=""
                                            class="img-fluid mb-3" style="max-width: 200px;">
                                        <span class="text-secondary">No Reviews Yet</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>


    <section class="news-letter">
        <div class="container">
            <div class="row">
                <div class="col-lg-7 m-auto">
                    <div class="news-letter-content d-flex flex-column text-center">
                        <h6 class="mb-3">Join our list to learn more</h6>
                        <p>Sign up to get updates on courses and events.</p>
                        <form action="" class="d-flex flex-column gap-3">
                            <input type="text" placeholder="Enter Your Email">
                            <button type="submit" class="button-primary my-3">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
    </section>
    <!-- Modal -->
    <div wire:ignore.self class="modal fade" id="courseEnrollment" tabindex="-1"
        aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title text-white fs-5" id="exampleModalLabel">Modal title</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure want to enroll in this course ?
                </div>
                <div class="modal-footer">
                    <button type="button" class="button-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" wire:click="enrollCourse" class="onsite-add-to-cart button-primary">Enroll
                        NOW</button>
                </div>
            </div>
        </div>
    </div>
</div>
