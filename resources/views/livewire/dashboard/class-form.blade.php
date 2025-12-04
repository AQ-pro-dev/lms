<div class="class_form">
    @push('title')
        {{ $type === 'class' ? 'Create Class' : 'Create Event' }}
    @endpush
    <header class="custom-header">
        <div class="container-fluid px-5">
            <div class="header-wrapper d-flex justify-content-between align-items-center mb-2">
                <img src="{{ asset('assets/svgs/Brand-NameHeader.png.svg') }}" alt="Logo" class="logo">
                <div class="actions">
                    {{-- <button type="button" class="btn button-draft custom-draft-button" wire:click="saveAsDraft">
                        <i class="fa-regular fa-floppy-disk me-1"></i>Save as Draft
                    </button> --}}
                    {{-- <button type="button" class="btn button-preview me-2 custom-preview-button">Preview</button> --}}
                    @if (!$classId && !$eventId)
                        <button type="submit" class="btn button-submit custom-submit-button" wire:click="save"
                            wire:loading.attr="disabled">
                            <span wire:loading wire:target="save" class="spinner-border spinner-border-sm"
                                aria-hidden="true"></span>
                            <span wire:loading.remove
                                wire:target="save">{{ $type === 'class' ? 'Create Class' : 'Create Event' }}</span>
                            <span wire:loading wire:target="save">Loading...</span>
                        </button>
                    @endif
                    @if ($classId || $eventId)
                        <button type="submit" class="btn button-submit custom-submit-button" wire:click="save"
                            wire:loading.attr="disabled">
                            <span wire:loading wire:target="updateClass" class="spinner-border spinner-border-sm"
                                aria-hidden="true"></span>
                            <span wire:loading.remove wire:target="updateClass">Update</span>
                            <span wire:loading wire:target="updateClass">Loading...</span>
                        </button>
                    @endif
                    <a href="{{ url()->previous() }}" class="custom-close-link ms-1">
                        <i class="fa-duotone fa-solid fa-xmark fa-2xl text-secondary"></i>
                    </a>
                </div>
            </div>
        </div>
    </header>
    <div class="container-fluid px-5 main-class-form pt-5">
        <div class="row g-4">
            <!-- Form Section -->
            <div class="col-xl-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header text-white">
                        <h5 class="mb-0">{{ $type === 'class' ? 'Create Class' : 'Create Event' }}</h5>
                    </div>
                    <div class="card-body">
                        <form class="form">
                            <!-- Select Type (Class or Event) -->
                            <div class="mb-3">
                                <label for="type" class="form-label">Select Type</label>
                                <select wire:model="type" id="type" class="form-select"
                                    wire:change="dispatchTypeChanged">
                                    <option value="class">Class</option>
                                    <option value="event">Event</option>
                                </select>
                                @error('type')
                                    <li class="text-danger"><span class=" small">{{ $message }}</span></li>
                                @enderror
                            </div>
                            <!-- Course Name -->
                            @if ($type === 'class')
                                <div class="mb-3">
                                    <label for="course" class="form-label">Course Name</label>
                                    <select id="course" wire:model="course_id" class="form-select">
                                        <option value="">Select Course</option>
                                        @foreach ($courses as $course)
                                            <option value="{{ $course->id }}">{{ $course->title }}</option>
                                        @endforeach
                                    </select>
                                    @error('course_id')
                                        <li class="text-danger"><span class=" small">{{ $message }}</span></li>
                                    @enderror
                                </div>
                            @endif
                            <!-- Title -->
                            <div class="mb-3">
                                <label for="title"
                                    class="form-label">{{ $type === 'class' ? 'Class Title' : 'Event Title' }}</label>
                                <input wire:model="title" id="title"
                                    class="form-control
                                    @error('title') is-invalid
                                    @elseif($title) is-valid
                                    @enderror"
                                    type="text" placeholder="Enter Title">

                                @error('title')
                                    <li class="text-danger"><span class=" small">{{ $message }}</span></li>
                                @enderror
                            </div>
                            <!-- Description -->
                            <div class="mb-3">
                                <label for="description" class="form-label">
                                    {{ $type === 'class' ? 'Class Description' : 'Event Description' }}
                                </label>
                                <textarea wire:model="description" id="description"
                                    class="form-control @if ($errors->has('description')) is-invalid @elseif ($description) is-valid @endif"
                                    placeholder="Enter Description"></textarea>
                                @error('description')
                                    <li class="text-danger"><span class="small">{{ $message }}</span></li>
                                @enderror
                            </div>

                            <!-- Class/Event Type -->
                            <div class="mb-3">
                                <label for="classType"
                                    class="form-label">{{ $type === 'class' ? 'Class Type' : 'Event Type' }}</label>
                                <select wire:model="class_type" id="classType" class="form-select"
                                    wire:change="dispatchClassTypeChanged">
                                    <option value="onsite">Onsite</option>
                                    <option value="virtual">Virtual</option>
                                    {{-- <option value="recorded">Recorded</option> --}}
                                </select>
                                @error('class_type')
                                    <li class="text-danger"><span class=" small">{{ $message }}</span></li>
                                @enderror
                            </div>
                            <!-- Dynamic Fields -->
                            @if ($class_type === 'virtual')
                                <div class="mb-3">
                                    <label for="teamsLink" class="form-label">Teams Link Will be Generated Automatically
                                        for the Class</label>
                                </div>
                            @endif
                            @if ($class_type === 'onsite')
                                <div class="">
                                    <label for="onsiteAddress" class="form-label">Onsite Address</label>
                                    <input wire:model.defer="onsite_address" id="onsiteAddress"
                                        class="form-control @if ($errors->has('onsite_address')) is-invalid @elseif ($onsite_address) is-valid @endif"
                                        type="text" placeholder="Enter Onsite Address">
                                    @error('onsite_address')
                                        <li class="text-danger"><span class="small">{{ $message }}</span></li>
                                    @enderror
                                </div>
                            @endif
                            {{-- @if ($class_type === 'recorded')
                                <div class="mb-3">
                                    <label for="recordedVideoUrl" class="form-label">Recorded Video URL</label>
                                    <input wire:model="recorded_video_url" id="recordedVideoUrl"
                                        class="form-control @if ($errors->has('recorded_video_url')) is-invalid @elseif ($recorded_video_url) is-valid @endif"
                                        type="url" placeholder="Enter Recorded Video URL">
                                    @error('recorded_video_url')
                                        <li class="text-danger"><span class="small">{{ $message }}</span></li>
                                    @enderror
                                </div>
                            @endif --}}
                            <!-- Payment Status -->
                            <div class="mb-3">
                                <label for="isPaid"
                                    class="form-label">{{ $type === 'class' ? 'Class Payment Status' : 'Event Payment Status' }}</label>
                                <select wire:model="is_paid" id="isPaid" class="form-select"
                                    wire:change="dispatchIsPaidChanged">
                                    <option value="free">Free</option>
                                    <option value="paid">Paid</option>
                                </select>
                                @error('is_paid')
                                    <li class="text-danger"><span class=" small">{{ $message }}</span></li>
                                @enderror
                            </div>
                            <!-- Price -->
                            @if ($is_paid === 'paid')
                                <div class="mb-3">
                                    <label for="price"
                                        class="form-label">{{ $type === 'class' ? 'Class Price' : 'Event Price' }}</label>
                                    <input wire:model="price" id="price"
                                        class="form-control @if ($errors->has('price')) is-invalid @elseif ($price) is-valid @endif"
                                        type="number" placeholder="Enter Price">
                                    @error('price')
                                        <li class="text-danger"><span class="small">{{ $message }}</span></li>
                                    @enderror
                                </div>
                            @endif
                            {{-- class_date and event_date --}}
                            <div class="mb-3">
                                <label for="classDate"
                                    class="form-label">{{ $type === 'class' ? 'Class Date' : 'Event date' }}</label>
                                <input wire:model="class_date" id="classDate"
                                    class="form-control @if ($errors->has('class_date')) is-invalid @elseif ($capacity) is-valid @endif"
                                    placeholder="Enter class Date">
                                @error('class_date')
                                    <li class="text-danger"><span class="small">{{ $message }}</span></li>
                                @enderror
                            </div>
                            {{-- class_time and event_time --}}
                            <div class="mb-3">
                                <label for="classtime"
                                    class="form-label">{{ $type === 'class' ? 'Class Time' : 'Event Time' }}</label>
                                <input wire:model="class_time" id="classtime"
                                    class="form-control @if ($errors->has('class_time')) is-invalid @elseif ($capacity) is-valid @endif"
                                    placeholder="Enter class Date">
                                @error('class_time')
                                    <li class="text-danger"><span class="small">{{ $message }}</span></li>
                                @enderror
                            </div>
                            <!-- Capacity -->
                            <div class="mb-3">
                                <label for="capacity"
                                    class="form-label">{{ $type === 'class' ? 'Class Capacity' : 'Event Capacity' }}</label>
                                <input wire:model="capacity" id="capacity"
                                    class="form-control @if ($errors->has('capacity')) is-invalid @elseif ($capacity) is-valid @endif"
                                    type="number" placeholder="Enter Capacity">
                                @error('capacity')
                                    <li class="text-danger"><span class="small">{{ $message }}</span></li>
                                @enderror
                            </div>

                            <!-- Start and End Dates -->
                            <div class="row">
                                <div class="mb-3" wire:ignore>
                                    <label for="calendar-input" class="form-label">
                                        {{ $type === 'class' ? 'Bookings Start Date to End Date' : 'Booking Start Date to End Date' }}
                                    </label>
                                    <input type="text" wire:model.lazy="date_range" id="calendar-input"
                                        class="form-control {{ $errors->has('date_range') ? 'is-invalid' : ($date_range ? 'is-valid' : '') }}"
                                        placeholder="@if ($date_range) {{ $date_range }} @else Select Date Range (e.g., YYYY-MM-DD to YYYY-MM-DD) @endif">
                                </div>
                            </div>
                            @error('date_range')
                                <li class="text-danger"><span class="small">{{ $message }}</span></li>
                            @enderror
                            @error('booking_end_date')
                                <li class="text-danger"><span class="small">{{ $message }}</span></li>
                            @enderror

                        </form>
                    </div>
                </div>
            </div>
            <!-- Additional Section for Future Widgets or Information -->
            <div class="col-xl-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-secondary text-white">
                        <h6 class="mb-0">Additional Information</h6>
                    </div>
                    <div class="card-body">
                        <ul class="text-muted custom-upload-tips-list">
                            <li>Create a class (linked to a course) or an event (e.g., webinar, workshop).</li>
                            <li>Choose class type: Virtual (requires Microsoft account) or On-site.</li>
                            <li>Set student capacity for the class.</li>
                            <li>Booking dates must be before the class date.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@script
    <script>
        flatpickr("#calendar-input", {
            mode: "range",
            // dateFormat: "Y-m-d",
            altInput: true,
            // altFormat: "F j, Y",
            // enableTime: true,
            minDate: "today",
            onChange: function(selectedDates) {
                // Convert the selected dates into the desired format
                const startDate = selectedDates[0] ? selectedDates[0].toLocaleString('en-CA') : '';
                const endDate = selectedDates[1] ? selectedDates[1].toLocaleString('en-CA') : '';
                @this.set('date_range', startDate + (endDate ? ' to ' + endDate : ''));
            }
        });

        flatpickr("#classDate", {
            dateFormat: "Y-m-d",
            altFormat: "F j, Y",
            defaultDate: new Date(),
        });

        flatpickr("#classtime", {
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            time_24hr: true
        });
    </script>
@endscript
