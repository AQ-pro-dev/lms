<div>
    @push('title')
        Calendar
    @endpush
    <section class="event-calendar my-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-9">
                    <div id="calendar" wire:ignore></div>
                </div>
                <div class="col-lg-3">
                    <div class="event-cards">
                        <input type="text" class="form-control mb-4" placeholder="Search">
                        @foreach ($eventCards as $event)
                            <button type="button" class="event-card mb-3 btn"
                                wire:click="selectEvent({{ json_encode($event) }}, 'eventCard')" data-bs-toggle="modal"
                                data-bs-target="#eventModal">
                                <div class="event-card-wrapper">
                                    <div class="date-badge me-3">
                                        <div class="month">
                                            {{ \Carbon\Carbon::parse($event['class_date'])->format('M') }}
                                        </div>
                                        <div class="day py-1">
                                            {{ \Carbon\Carbon::parse($event['class_date'])->format('d') }}
                                        </div>
                                        <div class="event-count mt-1 text-secondary">
                                            <i class="fas fa-user me-1"></i>{{ $event->capacity . ' slots' ?? 'N/A' }}
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <h6 class="mb-1 event-title">{{ $event['title'] ?? 'N/A' }}</h6>
                                    </div>
                                </div>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- EventCard Modal -->
    <div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true"
        wire:ignore.self>
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header text-white">
                    <h5 class="modal-title" id="event-modal-title">{{ $selectedEvent['title'] ?? 'Event Details' }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div wire:loading class="w-100">
                        <div class="d-flex justify-content-center align-items-center">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                    <div wire:loading.remove>
                        @if ($selectedEvent)
                            <div class="event-details">
                                <p><strong>Description:</strong>
                                    <span>{{ $selectedEvent['description'] ?? 'No description available' }}</span>
                                </p>
                                <p><strong>Spots Left:</strong> <span>{{ $selectedEvent['capacity'] ?? 'N/A' }}</span>
                                </p>
                                <p><strong>Booking Start Date:</strong>
                                    <span>{{ \Carbon\Carbon::parse($selectedEvent['booking_start_date'] ?? '')->toFormattedDateString() ?: 'N/A' }}</span>
                                </p>
                                <p><strong>Booking End Date:</strong>
                                    <span>{{ \Carbon\Carbon::parse($selectedEvent['booking_end_date'] ?? '')->toFormattedDateString() ?: 'N/A' }}</span>
                                </p>
                                <p><strong>Class Date:</strong>
                                    <span>{{ isset($selectedEvent['class_date']) ? \Carbon\Carbon::parse($selectedEvent['class_date'])->toFormattedDateString() : 'N/A' }}</span>
                                </p>
                                <p><strong>Class Time:</strong>
                                    <span>
                                        {{ isset($selectedEvent['class_time']) && $selectedEvent['class_time']
                                            ? \Carbon\Carbon::createFromFormat('H:i:s', $selectedEvent['class_time'])->format('h:i A')
                                            : 'N/A' }}
                                    </span>
                                </p>
                                @if (isset($selectedEvent['is_paid']) && $selectedEvent['is_paid'] == 'paid')
                                    <p><strong>Price:</strong>
                                        <span>
                                            {{ isset($selectedEvent['price']) ? number_format($selectedEvent['price'], 2) : 'N/A' }}
                                        </span>
                                    </p>
                                @endif
                            </div>

                            <hr>

                            <div class="hosted">
                                <h4 class="mb-3">Hosted by:</h4>
                                <div class="hosted-profile">
                                    @if (isset($selectedEvent['users']['pfp']))
                                        <img src="{{ asset('storage/' . $selectedEvent['users']['pfp']) }}"
                                            alt="profile">
                                    @else
                                        <img src="{{ asset('assets/images/dummy-profile-photo.webp') }}"
                                            alt="profile">
                                    @endif
                                    <p class="m-0"> {{ $selectedEvent['users']['first_name'] ?? 'N/A' }}
                                        {{ $selectedEvent['users']['last_name'] ?? '' }}</p>
                                </div>
                            </div>
                        @else
                            <div class="text-center">
                                <i class="fas fa-spinner fa-spin"></i> Loading...
                            </div>
                        @endif
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="button-secondary" data-bs-dismiss="modal">Close</button>
                    @if (auth()->check() && isset($selectedEvent['users']) && $selectedEvent['users']['id'] == auth()->user()->id)
                        <button type="button" class="btn btn-secondary" disabled>Enroll Now</button>
                    @else
                        <button type="button" class="button-primary" wire:click="enroll">Enroll Now</button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Calendar Event Modal -->
    <div class="modal fade" id="calendarEventModal" tabindex="-1" aria-labelledby="calendarEventModalLabel"
        aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header text-white">
                    <h5 class="modal-title" id="calendarEventModalLabel">Event Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="event-details">
                        <p><strong>Description:</strong> <span class="description">No description available</span></p>
                        <p><strong>Spots Left:</strong> <span class="spotsLeft">N/A</span></p>
                        <p><strong>Class will be on:</strong> <span class="date">N/A</span></p>
                        <p><strong>Class Time:</strong> <span class="class_time">N/A</span></p>
                        <p><strong>Location:</strong> <span class="location">N/A</span></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="button-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>



</div>
@push('js')
    <script>
        window.addEventListener('close-modal', event => {
            $('#eventModal').modal('hide');
        });
    </script>
@endpush
