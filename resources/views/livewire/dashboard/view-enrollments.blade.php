<div class="view-enrollments py-5 px-4">
    @push('title')
        View Enrollments
    @endpush
    @if (Auth::user()->role_id == '2' || Auth::user()->role_id == '1')
        <section class="enrollments mx-4">
            <div class="tutor-avatar d-flex align-items-center gap-2 mb-5">
                <img src=" {{ Auth::user()->pfp ? asset('storage/' . Auth::user()->pfp) : asset('assets/images/dummy-profile-photo.webp') }}"
                    alt="tutor" />
                <h6>{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}<h6>
            </div>
            <div class="row">
                <div class="col-xl-9 mb-3" wire:ignore>
                    <input type="text" class="mb-3" id="calendar-input" placeholder="Select date" />
                </div>
                @if (Auth::user()->role_id == '2' || Auth::user()->role_id == '1')
                    <div class="col-xl-3 mb-3">
                        <a href="{{ route('dashboard.create.class') }}" type="button" class="button-primary w-100">
                            <i class="fa-solid fa-plus"></i> New
                            Class/Event
                        </a>
                    </div>
                @endif
            </div>
        </section>
        <section class="classes enrollments my-5 mx-4">
            @if (collect($items)->isNotEmpty())
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Type</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Price</th>
                                <th class="">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($items as $item)
                                <tr>
                                    <td>{{ $item['title'] }}</td>
                                    <td>{{ $item['type'] }}</td>
                                    <td>{{ $item['date'] }}</td>
                                    <td>{{ $item['time'] }}</td>
                                    <td>
                                        @if ($item['price'] === 'Free' || $item['price'] === '$0.00')
                                            <span class="badge bg-info">Free</span>
                                        @else
                                            <span class="badge bg-success">{{ $item['price'] }}</span>
                                        @endif
                                    </td>


                                    <td>
                                        <button type="button"
                                            wire:click="openAttendeeModal({{ $item['id'] }}, '{{ $item['model_type'] }}')"
                                            data-bs-toggle="modal" data-bs-target="#attendee-modal"
                                            class="attendee-btn">
                                            <i class="fa-solid fa-user"></i>
                                        </button>


                                        <button class="view-btn"
                                            wire:click="selectItem({{ $item['id'] }}, '{{ $item['model_type'] }}')"
                                            data-bs-toggle="modal" data-bs-target="#viewItemModal">
                                            <i class="fa-regular fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="no-enrollment-img d-flex justify-content-center align-content-center mt-5">
                    <img src="{{ asset('assets/images/no-enrollment.webp') }}" alt="no-enrollment" />
                </div>
            @endif
        </section>
    @endif
    <section class="enrollments-table my-5 mx-4">
        <h4>Enrollments</h4>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Order #</th>
                        <th>Class/Event</th>
                        <th>Payment</th>
                        <th>Time</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($enrollments as $enrollment)
                        <tr>
                            <!-- Order Number -->
                            <td>{{ $enrollment->order_number }}</td>

                            <!-- Class/Event Title -->
                            <td>
                                @if ($enrollment->class_id)
                                    <span class="badge bg-primary"
                                        title="Class">{{ $enrollment->class->title }}</span>
                                @elseif ($enrollment->event_id)
                                    <span class="badge bg-success"
                                        title="Event">{{ $enrollment->event->title }}</span>
                                @else
                                    <span class="text-muted">No Enrollment</span>
                                @endif
                            </td>

                            <!-- Payment Status -->
                            <td>
                                @if ($enrollment->payment_status === 'paid')
                                    <span class="badge bg-success">Paid</span>
                                @else
                                    <span class="badge bg-info">Free</span>
                                @endif
                            </td>

                            <!-- Time and Date -->
                            @if ($enrollment->class_id)
                                <td>{{ \Carbon\Carbon::parse($enrollment->class->class_time, 'UTC')->setTimezone(Auth::user()->timezone)->format('g:i A') }}
                                </td>
                                <td>{{ \Carbon\Carbon::parse($enrollment->class->class_date, 'UTC')->setTimezone(Auth::user()->timezone)->format('F j, Y') }}
                                </td>
                            @elseif ($enrollment->event_id)
                                <td>{{ \Carbon\Carbon::parse($enrollment->event->class_time, 'UTC')->setTimezone(Auth::user()->timezone)->format('g:i A') }}
                                </td>
                                <td>{{ \Carbon\Carbon::parse($enrollment->event->class_date, 'UTC')->setTimezone(Auth::user()->timezone)->format('F j, Y') }}
                                </td>
                            @else
                                <td>N/A</td>
                                <td>N/A</td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">No enrollments found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
    <!-- modal -->
    <div wire:ignore.self class="modal fade modal-lg" id="viewItemModal" tabindex="-1"
        aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header text-white">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">
                        @if ($selectedClass)
                            Class Details
                        @elseif ($selectedEvent)
                            Event Details
                        @else
                            Details
                        @endif
                    </h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div wire:loading>
                        <p>Loading details...</p>
                    </div>
                    <div class="enrollment-modal-details" wire:loading.remove>
                        @if ($selectedClass)
                            <!-- Class Details -->
                            <p><strong>Title:</strong> {{ $selectedClass->title }}</p>
                            <p><strong>Description:</strong> {{ $selectedClass->description }}</p>
                            <p><strong>Capacity:</strong> {{ $selectedClass->capacity }}</p>
                            <p><strong>Class Type:</strong> {{ ucfirst($selectedClass->class_type) }}</p>
                            <p><strong>Class Date:</strong>
                                {{ $selectedClass->class_date ? \Carbon\Carbon::parse($selectedClass->class_date)->format('F, d Y') : 'Not Set' }}
                            </p>
                            <p><strong>Class Time:</strong>
                                {{ $selectedClass->class_time
                                    ? \Carbon\Carbon::parse($selectedClass->class_time, 'UTC')->setTimezone(Auth::user()->timezone)->format('g:i A')
                                    : 'Not Set' }}
                            </p>
                            <p><strong>Student Capacity:</strong> {{ $selectedClass->capacity }}</p>
                            @if ($selectedClass->class_type == 'recorded')
                                <p><strong>Video Link:</strong> <a href="{{ $selectedClass->recorded_video_url }}"
                                        target="_blank">{{ $selectedClass->recorded_video_url }}</a></p>
                            @elseif ($selectedClass->class_type == 'onsite')
                                <p><strong>Location:</strong> {{ $selectedClass->onsite_address }}</p>
                            @else
                                <p><strong>Virtual:</strong> <a href="{{ $selectedClass->teams_link }}"
                                        target="_blank">Join Via Teams</a></p>
                            @endif
                            <p><strong>Booking Start Date:</strong> {{ $selectedClass->booking_start_date }}</p>
                            <p><strong>Booking End Date:</strong> {{ $selectedClass->booking_end_date }}</p>
                            <p><strong>Price:</strong>
                                {{ $selectedClass->price ? '$' . number_format($selectedClass->price, 2) : 'Free' }}
                            </p>
                        @elseif ($selectedEvent)
                            <!-- Event Details -->
                            <p><strong>Title:</strong> {{ $selectedEvent->title }}</p>
                            <p><strong>Description:</strong> {{ $selectedEvent->description }}</p>
                            <p><strong>Event Date:</strong>
                                {{ $selectedEvent->class_date ? \Carbon\Carbon::parse($selectedEvent->class_date)->format('F, d Y') : 'Not Set' }}
                            </p>
                            <p><strong>Event Time:</strong>
                                {{ $selectedEvent->class_time
                                    ? \Carbon\Carbon::parse($selectedEvent->class_time, 'UTC')->setTimezone(Auth::user()->timezone)->format('g:i A')
                                    : 'Not Set' }}
                            </p>
                            @if ($selectedEvent->onsite_address)
                                <p><strong>Location:</strong> {{ $selectedEvent->onsite_address ?? 'Not Set' }}</p>
                            @elseif ($selectedEvent->teams_link)
                                <a href="{{ $selectedEvent->teams_link }}"><strong>Location: Join Via Teams</strong>
                                </a>
                            @endif
                            <p><strong>Price:</strong>
                                {{ $selectedEvent->price ? '$' . number_format($selectedEvent->price, 2) : 'Free' }}
                            </p>
                        @else
                            <p>No details available.</p>
                        @endif
                    </div>
                </div>
                <div class="modal-footer d-flex align-items-center justify-content-between py-2">
                    <div class="icons d-flex align-items-center justify-content-center gap-3">
                        <!-- Edit Icon -->
                        @if ($selectedClass || $selectedEvent)
                            <i type="button" class="edit-icon fa-solid fa-pen-to-square" wire:click="editItem"
                                data-bs-dismiss="modal"></i>
                        @endif
                        <!-- Delete Icon -->
                        @if ($selectedClass || $selectedEvent)
                            <i type="button" class="delete-icon fa-solid fa-trash" wire:click="deleteItem"></i>
                        @endif
                    </div>

                    <!-- Create Quiz Button (for Recorded Classes) -->
                    @if ($selectedClass && $selectedClass->class_type == 'recorded')
                        <button type="button" class="button-primary" wire:click="createQuiz">Create Quiz</button>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div wire:ignore.self class="modal fade" id="attendee-modal" tabindex="-1" role="dialog"
        aria-labelledby="attendeeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <h5 class="text-muted py-2" id="attendeeModalLabel">Attendees</h5>
                                <button type="button" class="btn button-primary w-100 mb-2"
                                    wire:click="addAttendeeField">+ Add Attendee</button>

                                @foreach ($newAttendees as $index => $newAttendee)
                                    <div class="row mb-3">
                                        <div wire class="col-8">
                                            <input type="email" class="form-control mb-3" placeholder="Email"
                                                wire:model="newAttendees.{{ $index }}.email">
                                            @error('email')
                                                <div class="text-danger">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                            <input type="text" class="form-control mb-3" placeholder="Phone"
                                                wire:model="newAttendees.{{ $index }}.phone">
                                            @error('phone')
                                                <div class="text-danger">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                            <select class="form-control mb-3"
                                                wire:model="newAttendees.{{ $index }}.payment_status">
                                                <option value="free">Free</option>
                                                <option value="unpaid">Unpaid</option>
                                                <option value="pending">Pending</option>
                                                <option value="paid">Paid</option>
                                                <option value="refunded">Refunded</option>
                                            </select>
                                            @error('payment_status')
                                                <div class="text-danger">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="col-4 d-flex align-items-center">
                                            <button type="button" class="button-secondary"
                                                wire:click="saveManualAttendees">
                                                Save Manual Attendees
                                            </button>
                                        </div>
                                    </div>
                                @endforeach <!-- Closing the newAttendees loop here -->

                                <div class="attendee-list mt-3">
                                    @foreach ($attendees as $index => $attendee)
                                        <div class="attendee py-4">
                                            <div class="row">
                                                <div class="col-8">
                                                    @if ($isEditing && $editAttendeeId == $attendee['id'])
                                                        <input class="form-control mb-3" type="email"
                                                            wire:model="attendeeData.email" placeholder="Email">
                                                        <input type="text" class="form-control mb-3"
                                                            placeholder="Phone" wire:model.defer="attendeeData.phone">
                                                        <select class="form-control mb-3"
                                                            wire:model.defer="attendeeData.payment_status">
                                                            <option value="free">Free</option>
                                                            <option value="unpaid">Unpaid</option>
                                                            <option value="pending">Pending</option>
                                                            <option value="paid">Paid</option>
                                                            <option value="refunded">Refunded</option>
                                                        </select>
                                                    @else
                                                        <input class="form-control mb-3" type="text"
                                                            value="{{ $attendee['email'] }}" disabled>
                                                        <input class="form-control mb-3" type="text"
                                                            value="{{ $attendee['phone'] }}" disabled>
                                                        <select class="form-control mb-3 text-muted" disabled>
                                                            <option value="free"
                                                                {{ $attendee['payment_status'] === 'free' ? 'selected' : '' }}>
                                                                Free</option>
                                                            <option value="unpaid"
                                                                {{ $attendee['payment_status'] === 'unpaid' ? 'selected' : '' }}>
                                                                Unpaid</option>
                                                            <option value="pending"
                                                                {{ $attendee['payment_status'] === 'pending' ? 'selected' : '' }}>
                                                                Pending</option>
                                                            <option value="paid"
                                                                {{ $attendee['payment_status'] === 'paid' ? 'selected' : '' }}>
                                                                Paid</option>
                                                            <option value="refunded"
                                                                {{ $attendee['payment_status'] === 'refunded' ? 'selected' : '' }}>
                                                                Refunded</option>
                                                        </select>
                                                    @endif
                                                </div>
                                                <div class="col-4 m-auto">
                                                    @if ($isEditing && $editAttendeeId == $attendee['id'])
                                                        <button class="btn w-100 btn-primary mb-3"
                                                            wire:click="saveAttendee({{ $attendee['id'] }})">Save</button>
                                                    @else
                                                        <button class="btn w-100 btn-primary mb-3"
                                                            wire:click="enableEdit({{ $attendee['id'] }})">Edit</button>
                                                    @endif
                                                    <button class="btn w-100 btn-danger"
                                                        wire:click="deleteAttendee({{ $attendee['id'] }})">Remove</button>
                                                </div>
                                            </div>
                                            <hr>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@push('js')
    <script>
        window.addEventListener('close-modal', event => {
            $('#viewItemModal').modal('hide');
        });
    </script>
@endpush
@script
    <script>
        flatpickr("#calendar-input", {
            mode: "range",
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "F j, Y",
            minDate: "2024-01-01",
            maxDate: "2030-12-31",
            onClose: function(selectedDates, dateStr, instance) {
                @this.set('dateRange', dateStr);
            }
        });
    </script>
@endscript
