<div class="quiz_form">
    <header class="custom-header">
        <div class="container-fluid px-5">
            <div class="header-wrapper d-flex justify-content-between align-items-center mb-2">
                <img src="{{ asset('assets/svgs/Brand-NameHeader.png.svg') }}" alt="Logo" class="logo">
                <div class="actions">
                    <button type="button" class="btn button-submit custom-submit-button" wire:click="submit"
                        wire:loading.attr="disabled">
                        <span wire:loading wire:target="submit" class="spinner-border spinner-border-sm"
                            aria-hidden="true"></span>
                        <span wire:loading.remove wire:target="submit">Create Quiz</span>
                    </button>
                    <a href="" class="custom-close-link ms-1">
                        <i class="fa-solid fa-xmark fa-2xl text-secondary"></i>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="container-fluid px-5 main-quiz-form pt-5">
        <div class="row g-4">
            <div class="col-xl-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header text-white">
                        <h5 class="mb-0">Create Quiz</h5>
                    </div>
                    <div class="card-body">
                        <form>
                            <div class="mb-3">
                                <label for="quizTitle" class="form-label">Quiz Title</label>
                                <input wire:model="title" id="quizTitle"
                                    class="form-control @error('title') is-invalid @enderror" type="text"
                                    placeholder="Enter Quiz Title">
                                @error('title')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Quiz Description</label>
                                <textarea wire:model="description" id="description" class="form-control @error('description') is-invalid @enderror"
                                    placeholder="Enter Description"></textarea>
                                @error('description')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>

                            @foreach ($questions as $index => $question)
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label">Question Text</label>
                                            <input wire:model="questions.{{ $index }}.question_text"
                                                class="form-control @error('questions.' . $index . '.question_text') is-invalid @enderror"
                                                type="text" placeholder="Enter Question">
                                            @error('questions.' . $index . '.question_text')
                                                <span class="text-danger small">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Points</label>
                                            <input wire:model="questions.{{ $index }}.points"
                                                class="form-control @error('questions.' . $index . '.points') is-invalid @enderror"
                                                type="number" placeholder="Enter Points">
                                            @error('questions.' . $index . '.points')
                                                <span class="text-danger small">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        @foreach ($question['answers'] as $answerIndex => $answer)
                                            <div class="row align-items-center mb-2">
                                                <div class="col-8">
                                                    <input
                                                        wire:model="questions.{{ $index }}.answers.{{ $answerIndex }}.answer_text"
                                                        class="form-control" type="text" placeholder="Enter Answer">
                                                </div>
                                                <div class="col-2">
                                                    <input
                                                        wire:click="markCorrectAnswer({{ $index }}, {{ $answerIndex }})"
                                                        class="form-check-input" type="radio"
                                                        name="questions-{{ $index }}-correct-answer">
                                                    <label class="form-check-label">Correct</label>
                                                </div>

                                                <div class="col-2">
                                                    <button type="button" class="btn btn-danger btn-sm"
                                                        wire:click="removeAnswer({{ $index }}, {{ $answerIndex }})">Remove</button>
                                                </div>
                                            </div>
                                        @endforeach

                                        <button type="button" class="btn btn-secondary btn-sm"
                                            wire:click="addAnswer({{ $index }})">+ Add Answer</button>
                                    </div>
                                </div>
                            @endforeach

                            <button type="button" class="btn btn-danger w-100 mb-3" wire:click="removeQuestion">
                                - Remove Question
                            </button>

                            <button type="button" class="btn btn-secondary w-100 mb-3" wire:click="addQuestion">+ Add
                                Question</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-secondary text-white">
                        <h6 class="mb-0">Additional Information</h6>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">You can add questions, set quiz timers, and assign to a class.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
