<div class="course-details">
    <section class="hero" style="background-image: url('{{ asset('storage/' . $coursePhoto) }}');">
        <div class="container">
            <div class="inner">
                <div class="hero-details">
                    <p class="course-heading-border">Course Description</p>
                    <h1>{{ $courseTitle }}</h1>
                    <p>
                        {{ $courseDescription }}
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="course-classes">
        <div class="container">
            <h2>Available Classes</h2>

            <div class="search-container">
                <div class="search-bar">
                    <img src="{{ asset('assets/svgs/search-icon.svg') }}" alt="Search icon" />
                    <input type="text" wire:model.live="searchKeyword" placeholder="Search classes by keyword" />
                </div>

                <button type="button" class="filter-btn">
                    <img src="{{ asset('assets/svgs/filter-icon.svg') }}" alt="Filter icon" /> Filters
                </button>
            </div>

            <div wire:ignore class="filter-row">
                <select class="location-dropdown mb-3" wire:model="selectedLocation" wire:change="fetch">
                    <option value="">Select location</option>
                    <option value="online">Online</option>
                    <option value="onsite">On-site</option>
                </select>
                <input type="text" id="calendar-input" class="calendar-input mb-3" wire:model="selectedDate"
                    placeholder="Select date" wire:change="fetch" />
            </div>
            @forelse ($classes as $class)
                <div class="event-card">
                    <div class="event-card__date">
                        <div class="day">{{ \Carbon\Carbon::parse($class->class_date)->format('d') }}</div>
                        <div class="month">{{ \Carbon\Carbon::parse($class->class_date)->format('M') }}</div>
                        <div class="time">
                            {{ \Carbon\Carbon::parse($class->class_date . ' ' . $class->class_time)->format('g:i A') }}
                        </div>
                    </div>
                    <div class="event-card__info mx-4">
                        <div class="event-name">{{ $class->title }}</div>
                        <div class="event-card__details d-flex">
                            @if ($class->capacity > 0)
                                <div class="status open me-2">Open</div>
                                <div class="capacity">
                                    {{ $class->capacity }}<span> spots available</span>
                                </div>
                            @else
                                <div class="status closed">Full</div>
                            @endif
                        </div>
                    </div>
                    <div class="event-card__actions">
                        <div class="price">
                            @if ($class->is_paid === 'paid' && $class->price > 0)
                                ${{ $class->price }}
                            @else
                                FREE
                            @endif
                        </div>

                        @php
                            $now = \Carbon\Carbon::now();
                            $bookingStart = \Carbon\Carbon::parse($class->booking_start_date);
                            $bookingEnd = \Carbon\Carbon::parse($class->booking_end_date);
                            // @dd($now, $bookingStart, $bookingEnd);
                        @endphp

                        @if ($now->lt($bookingStart))
                            <button type="button" class="enroll-btn" disabled>
                                Booking Starts: {{ $bookingStart->format('F j, Y') }}
                            </button>
                        @elseif ($now->gt($bookingEnd))
                            <button type="button" class="enroll-btn" disabled>
                                Booking Closed
                            </button>
                        @else
                            <button type="button" class="enroll-btn"
                                @if ($this->isEnrolled($class->id)) disabled
            @else
                wire:click="prepareEnrollment({{ $class->id }})"
                data-bs-toggle="modal"
                data-bs-target="#enrollModal" @endif>
                                {{ $this->isEnrolled($class->id) ? 'Enrolled' : 'Enroll Now' }}
                            </button>
                        @endif
                    </div>

                </div>
            @empty
                <p>No classes available for the selected criteria.</p>
            @endforelse
        </div>
    </section>

    <!-- Enroll Modal -->
    <div wire:ignore.self class="modal fade" id="enrollModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                {{-- <div class="modal-header">
                    <h5 class="modal-title">Enroll in {{ $selectedClassTitle }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div> --}}
                <div class="modal-header">
                    <h5 class="modal-title text-white" id="enrollModalLabel">Enroll in {{ $selectedClassTitle }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <p><strong>Title: </strong><span>{{ $selectedClassTitle }}</span></p>
                    <p><strong>type:</strong> <span>{{ $isPaid ? 'Paid' : 'Free' }}</span></p>
                    @if ($isPaid)
                        <p><strong>Price:</strong> <span>${{ $price }}</span></p>
                    @endif
                    <p><span class="d-block mb-3">Are you sure you want to enroll in this class?</span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="button-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button"
                        wire:click="enroll({{ $selectedClassId }}, '{{ $isPaid }}', {{ $isPaid ? $price : 0 }})"
                        class="button-primary" wire:loading.attr="disabled">
                        <span wire:loading.remove>Enroll Now</span>
                        <span wire:loading>
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Enrolling...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <section class="course-video">
        <div class="container">
            <div class="video">
                <iframe src="{{ asset('storage/' . $courseVideo) }}" frameborder="0" allowfullscreen></iframe>

                {{-- <video controls src="{{ asset('storage/' . $courseVideo) }}"></video> --}}
            </div>
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
                        <div class="course-details__widget my-4">
                            <h3>What Will You Learn?</h3>
                            <ul>
                                <li>{{ $learningOutcomes }}</li>
                            </ul>
                            <h3>Target Audience</h3>
                            <ul>
                                <li>{{ $targetAudience }}</li>
                            </ul>
                            <h3>Requirements</h3>
                            <ul>
                                <li>{{ $requirements }}</li>
                            </ul>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="new" role="tabpanel" aria-labelledby="new-tab">
                        <div>
                            <h3 class="text-left d-flex my-4 fs-5">Student Ratings & Reviews</h3>
                            <div class="d-flex flex-column align-items-center ">
                                {{-- <img src="{{ asset('assets/svgs/emptystate.svg') }}" alt=""> --}}
                                <span class="text-secondary">No Review Yet</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="news-letter">
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
</div>


@script
    <script>
        document.querySelector('.filter-btn').addEventListener('click', function() {
            document.querySelector('.filter-row').classList.toggle('show');
        });

        flatpickr("#calendar-input", {
            dateFormat: "Y-m-d",
            altFormat: "F j, Y",
            defaultDate: new Date(),
        });
        window.addEventListener('close-modal', event => {
            $('#enrollModal').modal('hide');
        });
    </script>
@endscript
