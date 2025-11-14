<?php

namespace App\Livewire;

use App\Models\Quiz;
use App\Models\Option;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Models\QuizAttempt;
use App\Models\Badge;
use Illuminate\Support\Facades\Cache;

class LiteraryQuiz extends Component
{
    public $quiz;
    public $questions;

    public $currentQuestionIndex = 0;
    public $score = 0;

    public $selectedAnswerId = null;
    public $isAnswered = false;
    public $quizCompleted = false;
    public $unlockedBadges = [];

    public function placeholder()
    {
        return view('livewire.quiz-placeholder');
    }

    public function mount()
    {
        $this->quiz = Cache::remember('quiz_do_dia', now()->addDay(), function () {
            return Quiz::where('ativo', true)->inRandomOrder()->first();
        });

        if (!$this->quiz) {
            $this->questions = collect();
            return;
        }

        $this->questions = $this->quiz->questions()->with('options')->get();

        $usuario = Auth::user();

        if ($usuario) {
            $todayAttempt = $usuario->quizAttempts()
                ->where('quiz_id', $this->quiz->id)
                ->whereDate('created_at', today())
                ->first();

            if ($todayAttempt) {
                $this->quizCompleted = true;
                $this->score = $todayAttempt->score;
            }
        }
    }

    public function getCurrentQuestionProperty()
    {
        return $this->questions[$this->currentQuestionIndex] ?? null;
    }

    public function answer($optionId)
    {
        if ($this->isAnswered) return;

        $this->isAnswered = true;
        $this->selectedAnswerId = $optionId;

        $option = Option::find($optionId);
        if ($option && $option->is_correct) {
            $this->score++;
        }
    }

    public function nextQuestion()
    {
        $this->isAnswered = false;
        $this->selectedAnswerId = null;

        if ($this->currentQuestionIndex + 1 == $this->questions->count()) {
            $this->quizCompleted = true;
            $this->saveResults();
        } else {
            $this->currentQuestionIndex++;
        }
    }

    public function saveResults()
    {
        $usuario = Auth::user();
        if (!$usuario) {
            return;
        }

        $usuario->quizAttempts()->create([
            'quiz_id' => $this->quiz->id,
            'score' => $this->score,
            'total_questions' => $this->questions->count(),
        ]);

        $this->unlockedBadges = [];

        $emblemaIdEspecifico = $this->quiz->badge_id;

        if ($emblemaIdEspecifico) {
            $jaPossui = $usuario->badges()->where('badge_id', $emblemaIdEspecifico)->exists();
            if (!$jaPossui) {
                $usuario->badges()->attach($emblemaIdEspecifico);
                $this->unlockedBadges[] = Badge::find($emblemaIdEspecifico);
            }
        }

        $totalQuizzesConcluidos = $usuario->quizAttempts()->count();

        $idsEmblemasJaPossuidos = $usuario->badges()
            ->where('tipo', 'quizzes_concluidos')
            ->pluck('badges.id')
            ->toArray();

        $emblemasDeContagemParaGanhar = Badge::where('tipo', 'quizzes_concluidos')
            ->where('requisito', '<=', $totalQuizzesConcluidos)
            ->whereNotIn('id', $idsEmblemasJaPossuidos)
            ->get();

        if ($emblemasDeContagemParaGanhar->isNotEmpty()) {
            foreach ($emblemasDeContagemParaGanhar as $badge) {
                $usuario->badges()->attach($badge->id);
                $this->unlockedBadges[] = $badge;
            }
        }
    }

    public function render()
    {
        return view('livewire.literary-quiz');
    }
}
