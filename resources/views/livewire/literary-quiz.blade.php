<div>
    @if($quizCompleted)

        <div class="text-center">
            <h3 class="text-2xl font-bold text-biblioteca-800 mb-2">Quiz Concluído!</h3>
            <p class="text-lg text-biblioteca-700 mb-6">
                Você acertou
                <span class="font-bold text-biblioteca-600">{{ $score }}</span> de
                <span class="font-bold">{{ $this->questions->count() }}</span> perguntas.
            </p>

            @if(!empty($unlockedBadges))
                <div class="space-y-4 mb-6">
                    @foreach($unlockedBadges as $badge)
                        <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <i class="{{ $badge->icon_class }} text-4xl text-yellow-500 mb-2"></i>
                            <h4 class="font-bold text-yellow-800">Novo Emblema Desbloqueado!</h4>
                            <p class="text-yellow-700 font-semibold">{{ $badge->nome }}</label>
                            <p class="text-yellow-600 text-sm">{{ $badge->descricao }}</p>
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="mt-4">
                <p class="text-biblioteca-600">Gostou do quiz sobre "{{ $quiz->titulo }}"?</p>
                <a href="#" class="inline-block mt-2 bg-biblioteca-700 text-white px-6 py-2 rounded-lg font-medium hover:bg-biblioteca-800">
                    Ver livros de {{ $quiz->titulo }}
                </a>
            </div>
        </div>

    @elseif($this->currentQuestion)

        <div>
            <p class="text-lg font-semibold text-biblioteca-800 mb-4">
                ({{ $this->currentQuestionIndex + 1 }}/{{ $this->questions->count() }})
                {{ $this->currentQuestion->texto_pergunta }}
            </p>

            <div class="space-y-3">
                @foreach($this->currentQuestion->options as $option)
                    <button
                        wire:click="answer({{ $option->id }})"
                        @if($isAnswered)
                            disabled
                        class="w-full text-left p-3 rounded-lg border
                                  {{ $option->is_correct ? 'bg-green-100 border-green-300 text-green-800' : '' }}
                                  {{ $selectedAnswerId == $option->id && !$option->is_correct ? 'bg-red-100 border-red-300 text-red-800' : '' }}
                                  {{ $selectedAnswerId != $option->id ? 'bg-gray-50 border-gray-200' : '' }}
                                  "
                        @else
                            class="w-full text-left p-3 rounded-lg border border-biblioteca-300 hover:bg-biblioteca-50"
                        @endif
                    >
                        {{ $option->texto_opcao }}
                    </button>
                @endforeach
            </div>

            @if($isAnswered)
                <div class="text-right mt-6">
                    <button wire:click="nextQuestion" class="bg-biblioteca-700 text-white px-6 py-2 rounded-lg font-medium hover:bg-biblioteca-800">
                        Próxima Pergunta
                    </button>
                </div>
            @endif
        </div>

    @else
        <div class="text-center text-biblioteca-600">
            <i class="bi bi-emoji-frown text-4xl mb-2"></i>
            <p>Nenhum quiz disponível no momento.</p>
            <p>Por favor, volte mais tarde!</p>
        </div>
    @endif
</div>
