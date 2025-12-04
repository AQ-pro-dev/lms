<div class="recorded-lecture">
    <div class="container">
        <div class="top-bar d-flex align-items-center justify-content-between mt-5">
            <ul class="d-flex align-items-end gap-3 gap-md-4">
                <li class="back align-self-center"><i class="fa-solid fa-arrow-left fa-2xl"></i></li>
                <li>
                    <span class="d-block fs-4">{{ $currentLecture->title }}</span>
                    <i class="fa-regular fa-folder-closed fa-sm"></i>
                    {{ $lectures->count() }} Topics
                </li>
                <li><i class="fa-regular fa-circle-play fa-sm video-player"></i> {{ $lectures->count() }}
                    Lectures</li>
                <li>
                    <i class="fa-regular fa-clock fa-sm"></i>
                    {{ gmdate('H:i:s', $lectures->sum('video_duration')) }}
                </li>
            </ul>
            <div class="controll-btn d-flex gap-4">
                @php
                    $prevLecture = $lectures->where('id', '<', $currentLecture->id)->sortByDesc('id')->first();
                    $prevLectureId = $prevLecture ? $prevLecture->id : null;
                    $nextLecture = $lectures->where('id', '>', $currentLecture->id)->sortByDesc('id')->first();
                    $nextLectureId = $nextLecture ? $nextLecture->id : null;
                @endphp


                <a class="button-primary {{ !$prevLectureId ? 'disabled' : '' }}"
                    {{ !$prevLectureId ? '' : 'wire:navigate' }}
                    href="{{ $prevLectureId ? route('recorded.lecture', ['courseId' => $courseId, 'lectureId' => $prevLectureId]) : 'javascript:void(0)' }}">
                    <i class="fa-solid fa-arrow-left fa-xs"></i> Previous
                </a>

                <a class="button-secondary {{ !$nextLectureId ? 'disabled' : '' }}"
                    {{ !$nextLectureId ? '' : 'wire:navigate' }}
                    href="{{ $nextLectureId ? route('recorded.lecture', ['courseId' => $courseId, 'lectureId' => $nextLectureId]) : 'javascript:void(0)' }}">
                    Next <i class="fa-solid fa-arrow-right fa-xs"></i>
                </a>
            </div>

        </div>
        <section class="lecture mt-5">
            <div class="row">
                <div class="col-12 col-xl-8">
                    <div class="iframe-container">
                        <iframe id="vimeo-player"
                            src="https://player.vimeo.com/video/{{ $videoDetails['uri'] ? explode('/', $videoDetails['uri'])[2] : '' }}"
                            width="640" height="360" frameborder="0" allow="autoplay; fullscreen" allowfullscreen>
                        </iframe>

                        {{-- <h3 class="mt-3">{{ $currentLecture->title }}</h3>
                        <ul class="d-flex justify-content-between mt-3">
                            <li>Last Updated <span>{{ $currentLecture->updated_at->format('F d, Y') }}</span></li>
                            <li>Comments: {{ $currentLecture->comments_count }}</li>
                        </ul> --}}
                    </div>
                </div>
                <div class="col-12 col-xl-4">
                    <div class="lectures-detail my-xl-0 my-5">
                        <div class="lecture-progress">
                            <div class="d-flex justify-content-between">
                                <p>Course Content</p>
                                <p>{{ $progressPercentage }}% completed</p>
                            </div>
                            <progress class="w-100" id="file" value="{{ $progressPercentage }}"
                                max="100">{{ $progressPercentage }}%</progress>
                            <div class="accordion mb-4" id="accordionPanelsStayOpenExample">
                                @foreach ($lectures as $lecture)
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button
                                                class="accordion-button {{ $lecture->id === $currentLecture->id ? '' : 'collapsed' }}"
                                                type="button" data-bs-toggle="collapse"
                                                data-bs-target="#lecture-{{ $lecture->id }}"
                                                aria-expanded="{{ $lecture->id === $currentLecture->id ? 'true' : 'false' }}"
                                                aria-controls="lecture-{{ $lecture->id }}">
                                                <div>
                                                    <span class="d-block">Lecture #{{ $loop->iteration }} <i
                                                            class="fa-solid fa-circle-exclamation ms-4"></i></span>
                                                    <ul class="d-flex gap-3 align-items-center my-4">
                                                        <li><i class="fa-regular fa-folder-closed fa-sm"></i>
                                                            {{ $lecture->order }} Topics</li>
                                                        <li><i class="fa-regular fa-circle-play fa-sm video-player"></i>
                                                            {{ $lecture->order }} Lectures</li>
                                                        <li><i class="fa-solid fa-check-double"></i>
                                                            @if ($lecture->watched)
                                                                100% finish (1/1)
                                                            @else
                                                                0% finish (0/1)
                                                            @endif
                                                        </li>
                                                    </ul>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="lecture-{{ $lecture->id }}"
                                            class="accordion-collapse collapse {{ $lecture->id === $currentLecture->id ? 'show' : '' }}">
                                            <a
                                                href="{{ route('recorded.lecture', ['courseId' => $courseId, 'lectureId' => $lecture->id]) }}">
                                                <div
                                                    class="accordion-body d-flex align-items-center justify-content-between text-success">
                                                    <div>
                                                        @if ($lecture->watched)
                                                            <input type="radio" checked>
                                                        @else
                                                            <input type="radio" disabled>
                                                        @endif
                                                        {{-- <input type="radio"
                                                            {{ $lecture->watched ? 'checked disabled' : 'disabled' }}> --}}
                                                        <span class="ms-2">{{ $lecture->title }}</span>
                                                    </div>
                                                    <div>
                                                        <span class="class-duration">
                                                            Total Duration:
                                                            {{ gmdate('H:i:s', $lecture->video_duration) }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                        </div>
                        @if ($allLecturesWatched)
                            <button wire:click="sendCertificate" class="btn btn-success">
                                <span wire:loading wire:target="sendCertificate">
                                    <span class="spinner-border spinner-border-sm" role="status"
                                        aria-hidden="true"></span> Get Certificate
                                </span>
                                <span wire:loading.remove wire:target="sendCertificate">
                                    Get Certificate
                                </span>
                            </button>

                            <button wire:click="showcertificate" class="btn btn-success">
                                <span wire:loading wire:target="showcertificate">
                                    <span class="spinner-border spinner-border-sm" role="status"
                                        aria-hidden="true"></span> Show Certificate
                                </span>
                                <span wire:loading.remove wire:target="showcertificate">
                                    Show Certificate
                                </span>
                            </button>
                        @endif

                    </div>
                </div>
            </div>
            <div class="row">
                <div class=" col-12 tabs">
                    <h3 class="mt-3">{{ $currentLecture->title }}</h3>
                    <ul class="d-flex justify-content-between mt-3">
                        <li>Last Updated <span>{{ $currentLecture->updated_at->format('F d, Y') }}</span></li>
                        <li>Comments: {{ $currentLecture->comments_count }}</li>
                    </ul>

                    <div class="course-details-tabs mt-5">
                        <!-- Tab Navigation -->
                        <ul class="nav nav-tabs mb-5" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active" id="overview-tab" data-bs-toggle="tab" href="#overview"
                                    role="tab" aria-controls="overview" aria-selected="true">Overview</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="exercise-files-tab" data-bs-toggle="tab" href="#exercise-files"
                                    role="tab" aria-controls="exercise-files" aria-selected="false"
                                    tabindex="-1">Exercise Files</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="comments-tab" data-bs-toggle="tab" href="#comments"
                                    role="tab" aria-controls="comments" aria-selected="false"
                                    tabindex="-1">Comments</a>
                            </li>
                        </ul>

                        <!-- Tab Content -->
                        <div class="tab-content" id="myTabContent">
                            <!-- Overview Tab Pane -->
                            <div class="tab-pane fade show active text-start" id="overview" role="tabpanel"
                                aria-labelledby="overview-tab">
                                <div class="course-details__overview my-3">
                                    <h4 class="mb-5">Course Overview</h4>
                                    <p>{{ $currentLecture->overview }}</p>
                                </div>
                            </div>

                            <!-- Exercise Files Tab Pane -->
                            <div class="tab-pane fade" id="exercise-files" role="tabpanel"
                                aria-labelledby="exercise-files-tab">
                                <div class="course-details__widget">
                                    <h4 class="mb-5">Exercise Files</h4>
                                    @if ($quiz)
                                        <a href="{{ route('quiz.take', ['quizId' => $quiz->id]) }}">Create Quiz</a>
                                    @endif
                                </div>
                            </div>

                            <!-- Comments Tab Pane -->
                            <div class="tab-pane fade" id="comments" role="tabpanel"
                                aria-labelledby="comments-tab">
                                <div class="course-details__comments">
                                    <h4 class="mb-5">Comments</h4>
                                    <form action="#" method="post" class="w-100">
                                        <div class="d-flex align-items-start gap-3 w-100">
                                            <div class="comment-avatar">
                                                <img src="{{ asset('assets/images/autumn-150x150.webp.webp') }}"
                                                    alt="User Avatar">
                                            </div>
                                            <div class="comment-textarea">
                                                <label for="commentTextarea" class="visually-hidden">Write your
                                                    comment</label>
                                                <textarea id="commentTextarea" name="comment" class="form-control" placeholder="Write your comment here…"
                                                    style="height: 100px;"></textarea>
                                                <button class="button-primary mt-3">Submit</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        {{-- <section>
            <div class="tabs">
                <div class="course-details-tabs mt-5">
                    <!-- Tab Navigation -->
                    <ul class="nav nav-tabs mb-5" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" id="overview-tab" data-bs-toggle="tab" href="#overview"
                                role="tab" aria-controls="overview" aria-selected="true">Overview</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="exercise-files-tab" data-bs-toggle="tab" href="#exercise-files"
                                role="tab" aria-controls="exercise-files" aria-selected="false"
                                tabindex="-1">Exercise Files</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="comments-tab" data-bs-toggle="tab" href="#comments"
                                role="tab" aria-controls="comments" aria-selected="false"
                                tabindex="-1">Comments</a>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content" id="myTabContent">
                        <!-- Overview Tab Pane -->
                        <div class="tab-pane fade show active text-start" id="overview" role="tabpanel"
                            aria-labelledby="overview-tab">
                            <div class="course-details__overview my-3">
                                <h4 class="mb-5">Course Overview</h4>
                                <p>{{ $currentLecture->overview }}</p>
                            </div>
                        </div>

                        <!-- Exercise Files Tab Pane -->
                        <div class="tab-pane fade" id="exercise-files" role="tabpanel"
                            aria-labelledby="exercise-files-tab">
                            <div class="course-details__widget">
                                <h4 class="mb-5">Exercise Files</h4>
                                @if ($quiz)
                                    <a href="{{ route('quiz.take', ['quizId' => $quiz->id]) }}">Create Quiz</a>
                                @endif
                            </div>
                        </div>

                        <!-- Comments Tab Pane -->
                        <div class="tab-pane fade" id="comments" role="tabpanel" aria-labelledby="comments-tab">
                            <div class="course-details__comments">
                                <h4 class="mb-5">Comments</h4>
                                <form action="#" method="post" class="w-100">
                                    <div class="d-flex align-items-start gap-3 w-100">
                                        <div class="comment-avatar">
                                            <img src="{{ asset('assets/images/autumn-150x150.webp.png') }}"
                                                alt="User Avatar">
                                        </div>
                                        <div class="comment-textarea">
                                            <label for="commentTextarea" class="visually-hidden">Write your
                                                comment</label>
                                            <textarea id="commentTextarea" name="comment" class="form-control" placeholder="Write your comment here…"
                                                style="height: 100px;"></textarea>
                                            <button class="button-primary mt-3">Submit</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section> --}}
    </div>
    <!-- Review and Rating Modal -->
    <div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true"
        wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-white" id="reviewModalLabel">Course Review & Rating</h5>
                    <button type="button" class="btn-close" id="review_modal_close"
                        wire:click="assigncertificate"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="submitReview">
                        <div class="mb-3">
                            <label class="form-label">Rating</label>
                            <div class="d-flex align-items-center justify-content-center">
                                @for ($i = 1; $i <= 5; $i++)
                                    <i class="bi {{ $rating >= $i ? 'bi-star-fill' : 'bi-star' }} text-warning fs-3"
                                        style="cursor: pointer;" wire:click="setRating({{ $i }})"></i>
                                @endfor
                            </div>
                            @error('rating')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="review" class="form-label">Review</label>
                            <textarea class="form-control" id="review" rows="3" wire:model="review"></textarea>
                            @error('review')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div style="z-index: " class="modal fade" id="certificateModal" tabindex="-1"
        aria-labelledby="certificateModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-white" id="certificateModalLabel">Your Certificate</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if ($cetificatepathformodal != null)
                        <iframe id="certificateFrame" src="{{ asset($cetificatepathformodal) }}" width="100%"
                            height="500px" frameborder="0"></iframe>
                    @else
                        <p>No certificate available.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://player.vimeo.com/api/player.js"></script>
<script>
    console.log('ggg');

    var iframe = document.getElementById('vimeo-player');
    var player = new Vimeo.Player(iframe);

    player.on('timeupdate', function(data) {
        // Check if 10 seconds or less remain in the video
        if (data.duration - data.seconds <= 10) {
            // Notify Livewire or backend when 10 seconds remain
            @this.call('markAsWatched', {{ $currentLecture->id }});

            // Remove the event listener to prevent multiple calls
            player.off('timeupdate');
        }
    });

    document.addEventListener('showCertificateModal', function() {
        $('#certificateModal').modal('show');
        console.log('hi there');
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        Livewire.on('open-review-modal', () => {
            setTimeout(() => {
                $('#reviewModal').modal('show');
            }, 2000);
        });
        Livewire.on('close-review-modal', () => {
            $('#reviewModal').modal('hide');
            $('#certificateModal').modal('show');
        });

    });
    document.addEventListener('congratulation', function() {
        // First confetti burst (from above, with full width)
        setTimeout(() => {
            confetti({
                particleCount: 700,
                spread: 300,
                origin: {
                    x: 0,
                    y: 0,
                }
            });
        }, 200);

        // Second confetti burst (slightly delayed)
        setTimeout(() => {
            confetti({
                particleCount: 700,
                spread: 300,
                origin: {
                    x: 1,
                    y: 0,
                }
            });
        }, 200);
    });
</script>
