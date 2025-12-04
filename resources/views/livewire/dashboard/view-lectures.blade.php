<div class="container mt-4">
    @push('title')
        view lectures
    @endpush
    <!-- Course Name -->
    <h3 class="mb-4">{{ $course->title }}</h3>

    <!-- Lectures List -->
    <div class="card">
        <div class="card-body">
            <h5>Lectures</h5>
            <ul class="list-group">
                @forelse($lectures as $lecture)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>{{ $lecture->order }}. {{ $lecture->title }}</strong>
                                <p class="mb-0 text-muted">{{ $lecture->description }}</p>
                            </div>
                            <span class="badge bg-primary rounded-pill">
                                {{ gmdate('H:i:s', $lecture->video_duration) }}
                            </span>
                        </div>
                        <div>
                            <button type="button" class="btn btn-primary" wire:click="editLecture({{ $lecture->id }})"
                                data-bs-toggle="modal" data-bs-target="#updateLectureModal">Update</button>
                            <button wire:click="confirmDelete({{ $lecture->id }})" class="btn btn-danger"
                                data-bs-toggle="modal" data-bs-target="#deleteModal">
                                Delete
                            </button>
                            @if ($lecture->quizzes && !$lecture->quizzes->count())
                                <a class="btn btn-success"
                                    href="{{ route('quizzes.create', ['lectureId' => $lecture->id]) }}">
                                    Create Quiz
                                </a>
                            @endif
                        </div>
                    </li>
                @empty
                    <li class="list-group-item text-center text-muted">No lectures available.</li>
                @endforelse
            </ul>
        </div>
    </div>

    <!-- Update Lecture Modal -->
    <div wire:ignore.self class="modal fade" id="updateLectureModal" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title text-white fs-5" id="exampleModalLabel">Update Lecture</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Lecture Title and Description Form -->
                    <form wire:submit.prevent="updateLecture">
                        <div class="mb-3">
                            <label for="lectureTitle" class="form-label">Title</label>
                            <input type="text" class="form-control" id="lectureTitle" wire:model="lectureTitle"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="lectureDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="lectureDescription" wire:model="lectureDescription" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="newVideo" class="form-label">New Video</label>
                            <input type="file" class="form-control" id="newVideo" wire:model="newVideo">
                        </div>

                        <!-- Loading indicator -->
                        <div wire:loading wire:target="newVideo">
                            <p>Uploading video...</p> <!-- You can replace this with a spinner -->
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">Save
                                changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Bootstrap Modal for Deleting a Lecture -->
    <div wire:ignore.self class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this lecture?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button wire:click="deleteLecture" class="btn btn-danger" data-bs-dismiss="modal">Delete</button>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

@push('js')
    <script>
        // Listen for the event to close the modal
        window.addEventListener('close-modal', event => {
            $('#updateLectureModal').modal('hide');
            $('#close-delete-modal').modal('hide');
        });
    </script>
@endpush
