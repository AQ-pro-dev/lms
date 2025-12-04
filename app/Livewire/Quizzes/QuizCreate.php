<?php

namespace App\Livewire\Quizzes;

use App\Models\Classe;
use App\Models\Quiz;
use App\Models\QuizAnswer;
use App\Models\QuizQuestion;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class QuizCreate extends Component
{
    use LivewireAlert;
    public $title, $lecture_id, $timer, $description;
    public $questions = [];

    public function resetForm()
    {
        $this->title = '';
        $this->lecture_id = null;
        $this->timer = '';
        $this->description = '';
        $this->questions = [];
    }


    public function mount($lectureId)
    {
        $this->lecture_id = $lectureId;
    }


    public function addQuestion()
    {
        if (count($this->questions) >= 10) {
            $this->alert('error', 'You cannot add more than 10 questions.');
            return;
        }

        $this->questions[] = [
            'question_text' => '',
            'points' => '',
            'answers' => [
                ['answer_text' => '', 'is_correct' => false],
                ['answer_text' => '', 'is_correct' => false],
            ],
        ];
    }

    public function removeQuestion()
    {
        if (count($this->questions) > 0) {
            array_pop($this->questions);
        } else {
            $this->alert('error', 'No questions to remove.');
        }
    }


    public function addAnswer($questionIndex)
    {
        if (count($this->questions[$questionIndex]['answers']) >= 4) {
            $this->alert('error', 'Each question cannot have more than 4 answers.');
            return;
        }

        $this->questions[$questionIndex]['answers'][] = ['answer_text' => '', 'is_correct' => false];
    }

    public function removeAnswer($questionIndex, $answerIndex)
    {
        unset($this->questions[$questionIndex]['answers'][$answerIndex]);
        $this->questions[$questionIndex]['answers'] = array_values($this->questions[$questionIndex]['answers']);
    }

    public function submit()
    {
        $this->validate(
            [
                'title' => 'required|string|max:20',
                'lecture_id' => 'required|integer|exists:lectures,id',
                'description' => 'nullable|string',
                'questions' => 'required|array|min:1',
                'questions.*.question_text' => 'required|string',
                'questions.*.points' => 'required|integer|max:10',
                'questions.*.answers' => 'required|array|min:2',
                'questions.*.answers.*.answer_text' => 'required|string',
                'questions.*.answers.*.is_correct' => 'boolean',
            ],
            [
                'title.required' => 'The quiz title is required.',
                'class_id.required' => 'A class must be selected.',
                'class_id.exists' => 'The selected class is invalid.',
                'timer.integer' => 'The timer must be a valid number.',
                'questions.required' => 'At least one question is required.',
                'questions.*.question_text.required' => 'Each question must have text.',
                'questions.*.points.required' => 'Each question must have points assigned.',
                'questions.*.points.max' => 'Points for a question cannot exceed 10.',
                'questions.*.answers.required' => 'Each question must have at least two answers.',
                'questions.*.answers.*.answer_text.required' => 'Answer text is required for all answers.',
            ]
        );

        foreach ($this->questions as $index => $question) {
            $correctAnswers = array_filter($question['answers'], fn($answer) => $answer['is_correct']);
            if (count($correctAnswers) !== 1) {
                $this->addError("questions.$index.answers", "Each question must have exactly one correct answer.");
                return;
            }
        }

        $quiz = Quiz::create([
            'lecture_id' => $this->lecture_id,
            'title' => $this->title,
            'description' => $this->description,
        ]);

        foreach ($this->questions as $question) {
            $newQuestion = QuizQuestion::create([
                'quiz_id' => $quiz->id,
                'question_text' => $question['question_text'],
                'points' => $question['points'],
            ]);

            foreach ($question['answers'] as $answer) {
                QuizAnswer::create([
                    'question_id' => $newQuestion->id,
                    'answer_text' => $answer['answer_text'],
                    'is_correct' => $answer['is_correct'],
                ]);
            }
        }


        $this->resetForm();
        $this->alert('success', 'Quiz created successfully!');
    }

    public function markCorrectAnswer($questionIndex, $answerIndex)
    {
        foreach ($this->questions[$questionIndex]['answers'] as &$answer) {
            $answer['is_correct'] = false;
        }
        $this->questions[$questionIndex]['answers'][$answerIndex]['is_correct'] = true;
    }


    public function render()
    {
        return view('livewire.quizzes.quiz-create')->layout('components.layouts.createCourse');
    }
}
