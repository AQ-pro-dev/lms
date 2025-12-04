<div class="create-course-main">
    @push('title')
        Create Course
    @endpush
    <header class="custom-header">
        <div class="container-fluid px-5">
            <div class="header-wrapper d-flex justify-content-between align-items-center mb-2">
                <img src="{{ asset('assets/svgs/Brand-NameHeader.png.svg') }}" alt="Logo" class="logo">
                <div class="actions">
                    <button type="button" class="btn button-draft custom-draft-button" wire:click="saveAsDraft">
                        <i class="fa-regular fa-floppy-disk me-1"></i>Save as Draft
                    </button>
                    <button type="button" class="btn button-preview me-2 custom-preview-button"
                        wire:click="previewCourse">Preview</button>
                    <button type="submit" class="btn button-submit custom-submit-button" wire:click="submitCourse"
                        wire:loading.attr="disabled">
                        <span wire:loading wire:target="submitCourse" class="spinner-border spinner-border-sm"
                            aria-hidden="true"></span>
                        <span wire:loading.remove
                            wire:target="submitCourse">{{ $courseId ? 'Update Course' : 'Create Course' }}</span>
                        <span wire:loading wire:target="submitCourse">Loading...</span>
                    </button>

                    <div wire:ignore>
                        <a href="{{ url()->previous() }}" class="custom-close-link ms-1">
                            <i class="fa-duotone fa-solid fa-xmark fa-2xl text-secondary"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- Loader Overlay -->
    <div id="loaderOverlay" wire:loading.class="d-flex" wire:loading wire:target="submitCourse"
        class="d-none align-items-center justify-content-center">
        <div class="overlay-bg"></div>
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
    <div class="container my-5">
        <form>
            <div class="row">
                <div class="col-lg-8" style="position: relative" class="form w-75 w-lg-50 w-md-75 w-sm-100 mx-auto">
                    <div wire:loading wire:target="submitCourse" class="loading-spinner">
                        <div class="spinner"></div>
                    </div>
                    <div class="accordion" id="courseAccordion">
                        <!-- Course Info Accordion Item -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingCourseInfo">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseCourseInfo" aria-expanded="true"
                                    aria-controls="collapseCourseInfo">
                                    Course Info
                                </button>
                            </h2>
                            <div id="collapseCourseInfo" class="accordion-collapse collapse show"
                                aria-labelledby="headingCourseInfo">
                                <div class="accordion-body">
                                    <div class="course-main-content mb-4">
                                        <!-- Course Title -->
                                        <div class="mb-3">
                                            <label for="courseTitle">Course Title</label>
                                            <input type="text" wire:model="courseTitle"
                                                class="form-control @if ($errors->has('courseTitle')) is-invalid @elseif ($courseTitle) is-valid @endif"
                                                id="courseTitle" required>
                                            @error('courseTitle')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <!-- Description -->
                                        <div class="mb-3">
                                            <label for="description">Description</label>
                                            <textarea wire:model="description"
                                                class="form-control @if ($errors->has('description')) is-invalid @elseif ($description) is-valid @endif"
                                                id="description" required>
                                            </textarea>
                                            @error('description')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <!-- Course Type -->
                                        <div class="mb-3">
                                            <label for="courseType">Course Type</label>
                                            <select wire:model="courseType" id="courseType"
                                                class="form-control @if ($errors->has('courseType')) is-invalid @elseif ($courseType) is-valid @endif"
                                                required wire:change="updateCourseType">
                                                <option value="">Select course type</option>
                                                <option value="classtype">Classes Based(Virtual & Onsite)</option>
                                                <option value="recorded">Recorded Lectures</option>
                                            </select>
                                            @error('courseType')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <!-- Payment Status and Price (conditionally displayed) -->
                                        @if ($courseType === 'recorded')
                                            <div class="mb-3">
                                                <label for="isPaid" class="form-label">Course Payment Status</label>
                                                <select wire:model="is_paid" id="isPaid"
                                                    class="form-select @if ($errors->has('is_paid')) is-invalid @elseif (!is_null($is_paid)) is-valid @endif"
                                                    wire:change="updateIsPaid">
                                                    <option value="">Select payment status</option>
                                                    <option value="Paid">Paid</option>
                                                    <option value="Free">Free</option>
                                                </select>
                                                @error('is_paid')
                                                    <li class="text-danger"><span class="small">{{ $message }}</span>
                                                    </li>
                                                @enderror
                                            </div>

                                            @if ($is_paid == 'Paid')
                                                <div class="mb-3">
                                                    <label for="price" class="form-label">Price</label>
                                                    <input type="number" wire:model="price" id="price"
                                                        class="form-control @if ($errors->has('price')) is-invalid @elseif ($price) is-valid @endif"
                                                        min="0" required>
                                                    @error('price')
                                                        <li class="text-danger"><span
                                                                class="small">{{ $message }}</span></li>
                                                    @enderror
                                                </div>
                                            @endif
                                        @endif


                                        <label for="category">Categories</label>
                                        <div wire:ignore class="d-flex align-items-center select-wrapper">
                                            <!-- Category Dropdown -->
                                            <select class="form-control" id="category" multiple required>
                                                @foreach ($categorylist as $cat)
                                                    <option value="{{ $cat->id }}"
                                                        {{ in_array($cat->id, $category) ? 'selected' : '' }}>
                                                        {{ $cat->category_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @error('category')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                        <!-- Course Thumbnail -->
                                        <div class="row mt-4 align-items-center justify-content-between">
                                            <div class="col-md-4">
                                                <label class="form-label"><strong>Course Thumbnail</strong></label>
                                                <div class="image-placeholder mb-3">
                                                    <!-- Display the uploaded image or fallback to a placeholder -->
                                                    <img src="{{ $file && method_exists($file, 'temporaryUrl') ? $file->temporaryUrl() : ($file ? asset('storage/' . $file) : asset('assets/images/dummy-course-img.webp')) }}"
                                                        style="width: 250px; height: 150px; object-fit: cover;"
                                                        alt="Course Thumbnail" id="previewImage">

                                                </div>
                                            </div>
                                            <div class="col-md-5 d-flex flex-column justify-content-center">
                                                <p class="mb-1 text-secondary">Recommended Size: 700x430 pixels</p>
                                                <p class="mb-2 text-secondary">Supported Formats: JPG, PNG</p>

                                                <!-- Loader spinner shown during upload -->
                                                <div wire:loading wire:target="file"
                                                    class="spinner-border text-success" role="status">
                                                    <span class="visually-hidden">Uploading...</span>
                                                </div>

                                                <!-- Input for file upload -->
                                                <label for="file-upload"
                                                    class="custom-file-upload button-primary mt-3">
                                                    <i class="bi bi-upload me-1"></i> Upload Image
                                                </label>
                                                <input wire:model="file" id="file-upload" type="file"
                                                    accept="image/png, image/jpeg" class="d-none" />
                                                @error('file')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Video Section -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingVideo">
                                <button
                                    class="accordion-button {{ $errors->has('videoFile') || $videoFile ? '' : 'collapsed' }}"
                                    type="button" data-bs-toggle="collapse" data-bs-target="#collapseVideo"
                                    aria-expanded="{{ $errors->has('videoFile') || $videoFile ? 'true' : 'false' }}"
                                    aria-controls="collapseVideo">
                                    Video
                                </button>
                            </h2>
                            <div id="collapseVideo"
                                class="accordion-collapse collapse {{ $errors->has('videoFile') || $videoFile ? 'show' : '' }}"
                                aria-labelledby="headingVideo">
                                <div class="accordion-body">
                                    <div class="row align-items-center">
                                        <!-- Upload Button -->
                                        <div class="col-md-6">
                                            <label for="videoUpload" class="form-label button-primary">Upload Video
                                                File</label>

                                            <!-- Loader -->
                                            <div wire:loading wire:target="videoFile" class="text-center mb-2">
                                                <div class="spinner-border text-primary" role="status">
                                                    <span class="visually-hidden">Uploading...</span>
                                                </div>
                                                <p>Uploading...</p>
                                            </div>

                                            <input type="file"
                                                class="form-control {{ $errors->has('videoFile') ? 'is-invalid' : ($videoFile ? 'is-valid' : '') }}"
                                                id="videoUpload" wire:model="videoFile" accept="video/*">

                                            @error('videoFile')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <!-- Video Preview -->
                                        <div class="col-md-6 text-center">
                                            @if ($videoFile)
                                                <div class="mt-3">
                                                    <label class="form-label">Video Preview:</label>
                                                    @if (method_exists($videoFile, 'temporaryUrl'))
                                                        <!-- Display video using temporaryUrl() for newly uploaded file -->
                                                        <video controls class="w-100 rounded border"
                                                            style="max-height: 250px;"
                                                            wire:key="video-{{ $videoFile->getClientOriginalName() }}">
                                                            <source src="{{ $videoFile->temporaryUrl() }}"
                                                                type="video/mp4">
                                                            Your browser does not support the video tag.
                                                        </video>
                                                    @else
                                                        <!-- Display existing video file from storage or database -->
                                                        <video controls class="w-100 rounded border"
                                                            style="max-height: 250px;">
                                                            <source src="{{ asset('storage/' . $videoFile) }}"
                                                                type="video/mp4">
                                                            Your browser does not support the video tag.
                                                        </video>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Instructors -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="add_instructors">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseInstructors" aria-expanded="false"
                                    aria-controls="collapseInstructors">
                                    Instructors
                                </button>
                            </h2>
                            <div id="collapseInstructors" class="accordion-collapse collapse show"
                                aria-labelledby="add_instructors">
                                <div class="accordion-body">
                                    <div class="mb-3">
                                        <div class="instructor-card">

                                            <div class="" id="instructorDropdown">
                                                <div wire:ignore class="d-flex align-items-center select-wrapper my-2">
                                                    <select class="form-control" id="tutorSelect" multiple
                                                        wire:model="tutors" required>
                                                        @foreach ($tutorList as $tutor)
                                                            <option value="{{ $tutor->user->id }}">
                                                                {{ $tutor->user->email }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                @error('tutors')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            {{-- <div class="add-instructor-btn">
                                                <button type="button" class="button-secondary"
                                                    id="addInstructorBtn">Add Instructor</button>
                                            </div> --}}
                                            @foreach ($selectedTutors as $tutor)
                                                <div class="instructor-card-wrapper my-3 d-flex align-items-center">
                                                    <div class="instructor-avatar">
                                                        <img height="50px" width="50px"
                                                            src="{{ $tutor->user->pfp ? asset('storage/' . $tutor->user->pfp) : asset('assets/images/dummy-profile-photo.webp') }}"
                                                            alt="instructor img" class="avatar-image me-2">
                                                    </div>
                                                    <div class="instructor-info">
                                                        <div class="instructor-header">
                                                            <span
                                                                class="instructor-name">{{ $tutor->user->first_name }}
                                                                {{ $tutor->user->last_name }}</span>
                                                            {{-- <img height="50px" width="50px"
                                                                src="{{ asset('storage/' . $tutor->user->pfp) }}"
                                                                alt="Instructor Badge" class="instructor-badge"> --}}
                                                        </div>
                                                        <div class="instructor-email">{{ $tutor->user->email }}
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Data -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="add_Additional_Data">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseAdditional_Data" aria-expanded="true"
                                    aria-controls="collapseAdditional_Data">
                                    Additional Data
                                </button>
                            </h2>
                            <div id="collapseAdditional_Data" class="accordion-collapse collapse show"
                                aria-labelledby="add_Additional_Data">
                                <div class="accordion-body">
                                    <!-- What Will I Learn? Textarea -->
                                    <div class="mb-3">
                                        <label for="learnDetails">What Will I Learn?</label>
                                        <textarea wire:model="learnDetails"
                                            class="form-control @if ($errors->has('learnDetails')) is-invalid @elseif ($learnDetails) is-valid @endif"
                                            id="learnDetails" placeholder="Write here the course benefits (One per line)">
                                            </textarea>
                                        @error('learnDetails')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <!-- Targeted Audience Textarea -->
                                    <div class="mb-3">
                                        <label for="audienceDetails">Targeted Audience</label>
                                        <textarea wire:model="audienceDetails"
                                            class="form-control @if ($errors->has('audienceDetails')) is-invalid @elseif ($audienceDetails) is-valid @endif"
                                            id="audienceDetails"
                                            placeholder="Specify the target audience that will benefit the most from the course. (One line per target audience)">
                                            </textarea>
                                        @error('audienceDetails')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Requirements/Instructions Textarea -->
                                    <div class="mb-3">
                                        <label for="requirements">Requirements/Instructions</label>
                                        <textarea wire:model="requirements"
                                            class="form-control @if ($errors->has('requirements')) is-invalid @elseif ($requirements) is-valid @endif"
                                            id="requirements" placeholder="Additional requirements or special instructions for the students. (One per line)">
                                        </textarea>
                                        @error('requirements')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>


                                    <!-- Tags Select2 Dropdown -->
                                    <label for="tags" class="me-2">Tags:</label>
                                    <div wire:ignore class="d-flex align-items-center select-wrapper">
                                        <select class="form-control select2 w-100" id="tags" multiple required>
                                            @foreach ($tagslist as $tag)
                                                <option value="{{ $tag->id }}"
                                                    {{ in_array($tag->id, $tags) ? 'selected' : '' }}>
                                                    {{ $tag->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('tags')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Course Upload Tips -->
                <div class="col-lg-4">
                    <div class="card p-3 mb-3 custom-upload-tips-card">
                        <h5 class="card-title">Course Upload Tips</h5>
                        <ul class="custom-upload-tips-list">
                            <li><strong>Set Course Price</strong> – Recorded courses can be either paid or free.</li>
                            <li><strong>Use 700x430 Thumbnail</strong> – Make sure the course thumbnail is the right
                                size.</li>
                            <li><strong>Upload a Course Video</strong> – Add a video that gives an overview of the
                                course.</li>
                            <li><strong>Use Course Builder</strong> – Create and organize lessons and classes.</li>
                            <li><strong>Add Topics</strong> – Structure your course with topics for better navigation.
                            </li>
                            <li><strong>Set Prerequisites</strong> – Specify any required courses before enrolling in
                                this one.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@script
    <script>
        $(document).ready(function() {
            // Initialize Select2 on the category, tags, and tutorSelect dropdowns
            $('#category, #tags, #tutorSelect').select2({
                // placeholder: 'Select options',
                allowClear: true,
                multiple: true,
                closeOnSelect: false
            });

            // Handle change events for category and tags
            $('#category').on('change', function() {
                let data = $(this).val(); // Get the selected values
                @this.set('category', data); // Set Livewire category model
            });

            $('#tags').on('change', function() {
                let data = $(this).val(); // Get the selected values
                @this.set('tags', data); // Set Livewire tags model
            });

            $('#tutorSelect').on('change', function() {
                let data = $(this).val(); // Get the selected values
                @this.set('tutors', data); // Set Livewire tutors model
            });

            // Listen for Livewire dispatch event 'clearSelect2' to clear Select2 fields
            Livewire.on('clearSelect2', () => {
                $('#category, #tags, #tutorSelect').val(null).trigger('change');
            });

            // Toggle the visibility of the instructor dropdown when the button is clicked
            // $('#addInstructorBtn').on('click', function() {
            //     $('#instructorDropdown').toggleClass('d-none');
            // });
        });
    </script>
@endscript
