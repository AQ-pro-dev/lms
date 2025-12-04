<div class="lecture_upload px-4 py-5">
     @push('title')
        Lecture Upload
    @endpush
    <form wire:submit.prevent="submit">
        <!-- Single Course Selection -->
        <div class="mb-3">
            <label for="course_id" class="form-label">Course</label>
            <select wire:model="course_id" class="form-control" id="course_id" wire:change="fetchLectures">
                <option value="">Select a course</option>
                @foreach ($courses as $course)
                    <option value="{{ $course->id }}">{{ $course->title }}</option>
                @endforeach
            </select>
            <div wire:loading wire:target="course_id" class="text-info mt-2">Loading lectures...</div>
            @error('course_id')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <!-- Dynamic Lecture Blocks -->
        @foreach ($lectures as $index => $lecture)
            <div class="lecture-block mb-5 border rounded p-3">
                <h5>Lecture {{ $index + 1 }}</h5>

                <!-- Lecture Title -->
                <div class="mb-3">
                    <label for="title_{{ $index }}" class="form-label">Title</label>
                    <input type="text" wire:model.defer="lectures.{{ $index }}.title" class="form-control"
                        id="title_{{ $index }}" placeholder="Lecture Title">
                    @error('lectures.' . $index . '.title')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Lecture Description -->
                <div class="mb-3">
                    <label for="description_{{ $index }}" class="form-label">Description</label>
                    <textarea wire:model.defer="lectures.{{ $index }}.description" class="form-control"
                        id="description_{{ $index }}" placeholder="Lecture Description"></textarea>
                    @error('lectures.' . $index . '.description')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Video File Upload -->
                <div class="mb-3">
                    <label for="video_file_{{ $index }}" class="form-label">Video File</label>
                    <input type="file" wire:model.defer="lectures.{{ $index }}.video_file"
                        class="form-control" id="video_file_{{ $index }}">
                    <div wire:loading wire:target="lectures.{{ $index }}.video_file" class="mt-2 text-info">
                        Uploading video...
                    </div>
                    @error('lectures.' . $index . '.video_file')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Lecture Order -->
                <div class="mb-3">
                    @if ($totalLectures)
                        <label for="order_{{ $index }}" class="form-label">Order</label>
                        <select wire:model.defer="lectures.{{ $index }}.order" class="form-control"
                            id="order_{{ $index }}" wire:change="updateorder">
                            <option value="">Select Order</option>
                            @for ($i = 1; $i <= $totalLectures; $i++)
                                @if (in_array($i, $usedOrders) && $lecture['order'] != $i)
                                @else
                                    <option value="{{ $i }}">
                                        {{ $i }}
                                    </option>
                                @endif
                            @endfor
                        </select>
                        <div wire:loading wire:target="lectures.{{ $index }}.order" class="text-info mt-2">
                            Updating order...
                        </div>
                        @error('lectures.' . $index . '.order')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    @endif
                </div>

                <!-- Remove Lecture Block -->
                <button type="button" class="btn btn-outline-danger"
                    wire:click.prevent="removeLectureBlock({{ $index }})">
                    <span wire:loading.remove wire:target="removeLectureBlock({{ $index }})">
                        Remove Lecture
                    </span>
                    <span wire:loading wire:target="removeLectureBlock({{ $index }})">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Remove
                        Lecture
                    </span>
                </button>
            </div>
        @endforeach

        <!-- Add and Submit Buttons -->
        <div class="d-flex justify-content-between">
            <button type="button" class="button-secondary" wire:click.prevent="addLectureBlock">
                <span wire:loading.remove wire:target="addLectureBlock">Add More Lectures</span>
                <span wire:loading wire:target="addLectureBlock">
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Add More
                    Lectures
                </span>
            </button>
            <button type="submit" class="button-primary">
                <span wire:loading.remove wire:target="submit">Submit Lectures</span>
                <span wire:loading wire:target="submit">
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Submit
                    Lectures
                </span>
            </button>
        </div>
    </form>
</div>
