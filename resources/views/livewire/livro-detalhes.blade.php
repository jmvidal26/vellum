<div>
    @if($showModal && $livro)
        <div
            x-data="{ show: @entangle('showModal') }"
            x-init="$watch('show', value => {
                if(value) { document.body.classList.add('overflow-hidden'); }
                else { document.body.classList.remove('overflow-hidden'); }
             })"
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
        >

            <div class="fixed inset-0 bg-black bg-opacity-70"></div>

            <div class="bg-white rounded-xl max-w-4xl w-full max-h-[90vh] overflow-hidden relative z-10 shadow-xl flex flex-col">

                <div class="flex justify-between items-start p-6 border-b border-biblioteca-200 flex-shrink-0">
                    <div>
                        <h2 class="text-2xl md:text-3xl font-bold text-biblioteca-800">{{ $livro->titulo }}</h2>

                        <div class="flex items-center gap-2 mt-2" title="Média de {{ $livro->rating }} de 5">
                            @php $rating = $livro->rating; @endphp
                            <div class="flex items-center">
                                @for ($i = 1; $i <= 5; $i++)
                                    @if ($i <= floor($rating))
                                        <i class="bi bi-star-fill text-yellow-500"></i>
                                    @elseif ($i - 0.5 <= $rating)
                                        <i class="bi bi-star-half text-yellow-500"></i>
                                    @else
                                        <i class="bi bi-star text-yellow-500"></i>
                                    @endif
                                @endfor
                            </div>
                            <span class="text-biblioteca-600 font-medium">{{ $rating }}</span>
                            <span class="text-biblioteca-500 text-sm">({{ $livro->avaliacoes->count() }} avaliações)</span>
                        </div>
                    </div>
                    <button wire:click="closeModal" class="text-biblioteca-500 hover:text-biblioteca-700 transition-colors ml-4">
                        <i class="bi bi-x-lg text-2xl"></i>
                    </button>
                </div>

                <div class="p-6 md:p-8 overflow-y-auto flex-1">

                    @php
                        $formatos = $livro->formatos;
                        $imagem = $formatos->firstWhere('media_type', 'image/jpeg');
                        $url = $imagem ? $imagem->url : ($formatos->first()->url ?? null);
                    @endphp

                    <div class="flex flex-col md:flex-row gap-6 md:gap-8 mb-6">

                        <div class="flex-shrink-0 w-full md:w-56 mx-auto md:mx-0">
                            @if($url)
                                <img src="{{ $url }}"
                                     alt="Capa do livro {{ $livro->titulo }}"
                                     class="w-56 aspect-[2/3] object-cover rounded-lg shadow-lg mx-auto">
                            @else
                                <div class="w-56 aspect-[2/3] bg-biblioteca-100 flex items-center justify-center rounded-lg shadow-md mx-auto">
                                    <i class="bi bi-book text-6xl text-biblioteca-400"></i>
                                </div>
                            @endif
                        </div>

                        <div class="flex-1">

                            <div class="flex flex-col md:flex-row gap-4 mb-6">

                                <button
                                    wire:click="toggleFavorite"
                                    wire:loading.attr="disabled"
                                    class="flex-1 flex items-center justify-center gap-2 px-6 py-3 rounded-lg font-medium transition-colors
                                       {{ $isFavorito
                                            ? 'bg-red-50 text-red-700 border border-red-300 hover:bg-red-100'
                                            : 'bg-biblioteca-100 text-biblioteca-700 border border-biblioteca-200 hover:bg-biblioteca-200'
                                       }}"
                                >
                                    <span wire:loading.remove wire:target="toggleFavorite">
                                        @if($isFavorito)
                                            <i class="bi bi-heart-fill"></i> <span>Remover</span>
                                        @else
                                            <i class="bi bi-heart"></i> <span>Favoritar</span>
                                        @endif
                                    </span>
                                    <span wire:loading wire:target="toggleFavorite">Atualizando...</span>
                                </button>

                                <div x-data="{ open: false }" class="relative flex-1">
                                    <button
                                        @click="open = !open"
                                        class="w-full flex items-center justify-between gap-2 px-6 py-3 rounded-lg font-medium transition-colors bg-biblioteca-100 text-biblioteca-700 border border-biblioteca-200 hover:bg-biblioteca-200"
                                    >
                                        <div class="flex items-center gap-2">
                                            <i class="bi bi-bookmark-plus"></i>
                                            <span>Coleções</span>
                                        </div>
                                        <i class="bi bi-chevron-down transition-transform" :class="{ 'rotate-180': open }"></i>
                                    </button>

                                    <div
                                        x-show="open"
                                        @click.away="open = false"
                                        x-transition:enter="transition ease-out duration-100"
                                        x-transition:enter-start="opacity-0 scale-95"
                                        x-transition:enter-end="opacity-100 scale-100"
                                        x-transition:leave="transition ease-in duration-75"
                                        x-transition:leave-start="opacity-100 scale-100"
                                        x-transition:leave-end="opacity-0 scale-95"
                                        class="absolute z-10 w-full mt-2 bg-white border border-biblioteca-200 rounded-lg shadow-lg max-h-48 overflow-y-auto"
                                        x-cloak
                                    >
                                        @forelse($colecoes as $colecao)
                                            <label
                                                wire:key="colecao-{{ $colecao->id }}"
                                                class="flex items-center gap-3 px-4 py-3 hover:bg-biblioteca-50 cursor-pointer"
                                            >
                                                <input
                                                    type="checkbox"
                                                    class="rounded text-biblioteca-600 focus:ring-biblioteca-500 border-biblioteca-300"
                                                    wire:click="toggleColecao({{ $colecao->id }})"
                                                    {{ in_array($colecao->id, $livroColecaoIds) ? 'checked' : '' }}
                                                >
                                                <span class="text-biblioteca-700">{{ $colecao->nome }}</span>

                                                <div wire:loading wire:target="toggleColecao({{ $colecao->id }})">
                                                    <i class="bi bi-arrow-clockwise animate-spin text-biblioteca-500"></i>
                                                </div>
                                            </label>
                                        @empty
                                            <div class="px-4 py-3 text-biblioteca-500 text-sm">
                                                Nenhuma pasta criada.
                                            </div>
                                        @endforelse
                                    </div>
                                </div>

                            </div> <div class="mb-6">
                                <h3 class="text-xl font-bold text-biblioteca-800 mb-2">Sua Avaliação</h3>

                                <div
                                    x-data="{
                                         hoverRating: 0,
                                         currentRating: {{ $userRating }}
                                     }"
                                    @rating-updated.window="currentRating = $event.detail.rating"
                                    @mouseleave="hoverRating = 0"
                                    class="flex items-center gap-1"
                                    title="Sua avaliação: {{ $userRating > 0 ? $userRating : 'Nenhuma' }}">

                                    <template x-for="i in 5" :key="i">
                                        <button @click.prevent="$wire.setRating(i)"
                                                @mouseenter="hoverRating = i"
                                                class="text-3xl transition-colors duration-100 focus:outline-none">

                                            <i class="bi bi-star-fill"
                                               x-show="(hoverRating || currentRating) >= i"
                                               :class="(hoverRating && hoverRating >= i) ? 'text-yellow-400' : 'text-yellow-500'">
                                            </i>
                                            <i class="bi bi-star"
                                               x-show="!((hoverRating || currentRating) >= i)"
                                               class="text-biblioteca-300 hover:text-yellow-400">
                                            </i>
                                        </button>
                                    </template>
                                </div>
                            </div>

                            <div class="mb-6">
                                <h3 class="text-xl font-bold text-biblioteca-800 mb-3">Autores</h3>
                                <div class="flex flex-wrap gap-2">
                                    @forelse($livro->autores as $autor)
                                        <span class="bg-biblioteca-100 text-biblioteca-800 px-3 py-1 rounded-full text-sm font-medium">{{ $autor->nome }}</span>
                                    @empty
                                        <span class="text-biblioteca-600 text-sm">Não identificado</span>
                                    @endforelse
                                </div>
                            </div>

                            <div>
                                <h3 class="text-xl font-bold text-biblioteca-800 mb-3">Detalhes</h3>
                                <div class="space-y-4">
                                    <div class="flex items-start gap-3">
                                        <i class="bi bi-tag-fill text-xl text-biblioteca-600 mt-1"></i>
                                        <div>
                                            <div class="flex flex-wrap gap-2">
                                                @forelse($mainGenres as $genre)
                                                    <span class="bg-biblioteca-700 text-white px-3 py-1 rounded-full text-sm font-semibold">
                                                        {{ $genre }}
                                                    </span>
                                                @empty
                                                    <span class="text-biblioteca-600 text-sm">Sem gênero principal</span>
                                                @endforelse
                                            </div>
                                            @if(!empty($allShelves))
                                                <p class="text-xs text-biblioteca-500 mt-2">
                                                    Outras tags: {{ collect($allShelves)->take(5)->implode(', ') }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <i class="bi bi-graph-up text-xl text-biblioteca-600"></i>
                                        <span class="text-biblioteca-700">
                                            {{ $livro->numero_downloads }} downloads
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($livro->resumo)
                        <div class="border-t border-biblioteca-200 pt-6">
                            <h3 class="text-xl font-bold text-biblioteca-800">Resumo</h3>
                            <p class="text-biblioteca-700 leading-relaxed text-justify whitespace-pre-wrap">
                                {{ $livro->resumo }}
                            </p>
                        </div>
                    @endif
                </div>

                <div class="border-t border-biblioteca-200 p-6 bg-biblioteca-50 flex-shrink-0">
                    <div class="flex justify-end gap-3">
                        <button
                            wire:click="closeModal"
                            class="px-6 py-2 bg-white text-biblioteca-700 border border-biblioteca-300 rounded-lg hover:bg-biblioteca-100 transition-colors font-medium"
                        >
                            Fechar
                        </button>

                        <button
                            @click="$dispatch('carregarLivroPeloId', { livroId: {{ $livro->id }} })"
                            class="px-6 py-2 bg-biblioteca-700 text-white rounded-lg hover:bg-biblioteca-800 transition-colors font-medium flex items-center gap-2"
                        >
                            <i class="bi bi-book"></i>
                            <span>Ler Livro</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('modalFechado', () => {
            document.body.classList.remove('overflow-hidden');
        });
    });
</script>


