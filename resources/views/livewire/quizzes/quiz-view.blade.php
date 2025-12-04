<div class="container mt-5">
    <h2>{{ $quiz->title }}</h2>
    <p>{{ $quiz->description }}</p>

    <form wire:submit.prevent="submit">
        @foreach ($quiz->questions as $index => $question)
            <div class="mb-4">
                <h5>{{ $question->question_text }}</h5>
                <p>Points: {{ $question->points }}</p>

                @foreach ($question->answers as $answer)
                    <div class="form-check">
                        <input type="radio" id="answer-{{ $answer->id }}" class="form-check-input"
                            wire:model="answers.{{ $index }}" value="{{ $answer->id }}">
                        <label for="answer-{{ $answer->id }}" class="form-check-label">
                            {{ $answer->answer_text }}
                        </label>
                    </div>
                @endforeach
            </div>
        @endforeach

        <button type="submit" class="btn btn-primary">Submit Quiz</button>
    </form>

    @if (session()->has('success'))
        <div class="alert alert-success mt-3">
            {{ session('success') }}
        </div>
    @endif
</div>
