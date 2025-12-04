<?php

namespace App\Livewire\Quizzes;

use App\Models\Quiz;
use App\Models\QuizSubmission;
use Livewire\Component;

class QuizView extends Component
{
    public $quiz;
    public $answers = [];

    public function mount($quizId)
    {
        $this->quiz = Quiz::with('questions.answers')->findOrFail($quizId);
    }

    public function submit()
    {
        // Handle the quiz submission logic (e.g., calculate the score)
        $score = 0;

        foreach ($this->quiz->questions as $index => $question) {
            $correctAnswer = $question->answers->where('is_correct', true)->first();
            if (isset($this->answers[$index]) && $this->answers[$index] == $correctAnswer->id) {
                $score += $question->points;
            }
        }

        QuizSubmission::create([
            'quiz_id' => $this->quiz->id,
            'user_id' => auth()->id(), // Assuming the user is logged in
            'score' => $score,
        ]);

        session()->flash('success', "You scored $score points!");
    }

    public function render()
    {
        return view('livewire.quizzes.quiz-view');
    }
}
