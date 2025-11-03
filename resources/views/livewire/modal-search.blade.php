<div>
@if($isOpen)
    <div
    x-data="{}"
    @focus-search-input.window="$nextTick(() => $refs.modalSearchInput.focus())"
>

    <div wire:ignore.self
         x-show="$wire.isOpen"
         x-cloak
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @keydown.escape.window="$wire.close()"
         class="fixed inset-0 bg-black bg-opacity-70 z-[60]">
    </div>

    <div wire:ignore.self
         x-show="$wire.isOpen"
         x-cloak
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         @click.away="$wire.close()"
         class="fixed top-20 sm:top-1/4 left-1/2 -translate-x-1/2 sm:-translate-y-1/4 w-full max-w-xl p-4 sm:p-6 z-[70]">

        <div class="bg-white rounded-lg shadow-xl overflow-hidden">
            <div class="flex justify-between items-center p-4 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-biblioteca-700">Explorar o Acervo</h3>
                <button @click="$wire.close()" class="text-gray-500 hover:text-gray-700 p-1 rounded-full hover:bg-gray-100 transition-colors duration-200">
                    <i class="bi bi-x-lg text-xl"></i>
                </button>
            </div>

            <div class="p-4 sm:p-6">
                <form wire:submit="performSearch">
                    <label for="modal-search" class="sr-only">Buscar por título ou autor:</label>
                    <div class="relative flex items-center border border-gray-300 rounded-lg focus-within:ring-2 focus-within:ring-biblioteca-500">
                        <input
                            x-ref="modalSearchInput"
                            type="text"
                            id="modal-search"
                            placeholder="Título, autor, gênero..."
                            class="flex-grow text-gray-700 px-4 py-3 rounded-l-lg border-none focus:outline-none bg-transparent"
                            autocomplete="off"
                            wire:model.live.debounce.300ms="busca"
                        >
                        <button type="submit" class="bg-biblioteca-600 hover:bg-biblioteca-700 text-white px-5 py-3 rounded-r-lg flex items-center justify-center transition-colors duration-200">
                            <i class="bi bi-search text-lg"></i>
                            <span class="ml-2 hidden sm:inline">Buscar</span>
                        </button>
                    </div>
                </form>
            </div>

            <div class="px-2 sm:px-6 pb-4 max-h-[50vh] overflow-y-auto">

                <div wire:loading wire:target="busca" class="py-4 px-2 text-center text-gray-500">
                    <i class="bi bi-arrow-repeat animate-spin mr-2"></i> Buscando...
                </div>


                <div wire:loading.remove wire:target="busca">
                    @if (strlen($busca) < 3)
                        <div class="space-y-4">
                            @if (!empty($recentSearches))
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-600 mb-2">Buscas Recentes</h4>
                                    <ul class="flex flex-wrap gap-2">
                                        @foreach ($recentSearches as $term)
                                            <li>
                                                <button wire:click.prevent="searchFor('{{ $term }}')"
                                                        class="text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1 rounded-full transition-colors">
                                                    <i class="bi bi-clock-history mr-1"></i> {{ $term }}
                                                </button>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @if (!empty($popularSearches))
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-600 mb-2">Populares</h4>
                                    <ul class="flex flex-wrap gap-2">
                                        @foreach ($popularSearches as $term)
                                            <li>
                                                <button wire:click.prevent="searchFor('{{ $term }}')"
                                                        class="text-sm bg-biblioteca-100 hover:bg-biblioteca-200 text-biblioteca-700 px-3 py-1 rounded-full transition-colors">
                                                    <i class="bi bi-fire mr-1"></i> {{ $term }}
                                                </button>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    @else
                        @forelse ($results as $livro)
                            <button
                                wire:click="abrirDetalhesLivro({{ $livro->id }})"
                                class="flex items-center p-3 hover:bg-gray-100 rounded-lg transition-colors w-full text-left"
                            >
                                <img src="{{ $livro->url_capa ?? 'https://via.placeholder.com/40x60' }}"
                                     alt="Capa de {{ $livro->titulo }}"
                                     class="w-10 h-14 object-cover rounded shadow-md mr-4 flex-shrink-0">

                                <div class="text-gray-800 overflow-hidden">
                                    <p class="font-semibold truncate">{{ $livro->titulo }}</p>
                                    <p class="text-sm text-gray-600 truncate">
                                        {{ $livro->autores->pluck('nome')->implode(', ') }}
                                    </p>
                                </div>
                            </button>
                        @empty

                            @if (strlen($busca) >= 3)
                                <div class="p-3 text-center text-gray-500">
                                    Nenhum resultado encontrado para "{{ $busca }}".
                                </div>
                            @endif
                        @endforelse
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endif
</div>
