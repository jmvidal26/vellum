<li class="splide__slide list-none" wire:ignore.self>
    <a href="javascript:void(0)" wire:click="openModal({{ $livro['id'] }})" class="block h-full group relative transition-all duration-300 hover:z-20" style="padding-left: 10px; padding-right: 10px;">
        <div class="bg-white rounded-lg shadow-lg overflow-visible group-hover:shadow-xl transition-shadow duration-300 h-full {{ $size === 'large' ? 'max-w-xs' : '' }}">

            @php
                $formatos = is_array($livro['formatos'] ?? null) ? $livro['formatos'] : $livro->formatos;
                $formatoImagem = collect($formatos)->firstWhere('media_type', 'image/jpeg');
                $urlCapa = $formatoImagem['url'] ?? null;
            @endphp

            @if($urlCapa)
                <img src="{{ $urlCapa }}"
                     alt="Capa do livro {{ $livro['titulo'] ?? $livro->titulo }}"
                     class="w-full object-cover aspect-[2/3] rounded-t-lg group-hover:scale-105 transition-transform duration-300">
            @else
                <div class="w-full bg-biblioteca-100 aspect-[2/3] flex items-center justify-center rounded-t-lg group-hover:scale-105 transition-transform duration-300">
                    <i class="bi bi-book text-4xl text-biblioteca-400"></i>
                </div>
            @endif


            <div class="p-4 {{ $size === 'large' ? 'space-y-2' : '' }}">
                <h4 class="font-bold text-biblioteca-800 {{ $size === 'large' ? 'text-lg' : 'text-md' }} truncate"
                    title="{{ $livro['titulo'] ?? $livro->titulo }}">
                    {{ $livro['titulo'] ?? $livro->titulo }}
                </h4>

                @php
                    if (is_array($livro)) {
                        $autores = collect($livro['autores'] ?? [])->pluck('nome')->implode(', ');
                    } else {
                        $autores = $livro->autores->pluck('nome')->implode(', ');
                    }
                @endphp
                <p class="text-biblioteca-600 {{ $size === 'large' ? 'text-base' : 'text-sm' }} truncate" title="{{ $autores ?: 'Não identificado' }}">
                    {{ $autores ?: 'Não identificado' }}
                </p>

                <p class="text-biblioteca-500 {{ $size === 'large' ? 'text-sm' : 'text-xs' }} mt-2">
                    {{ $livro['numero_downloads'] ?? $livro->numero_downloads }} downloads
                </p>

                <div class="flex justify-between items-center mt-3">
                    @auth
                        <button
                            type="button"
                        wire:click.stop="toggleFavorite({{ $livro['id'] ?? $livro->id }})"
                            wire:loading.attr="disabled"
                            class="text-biblioteca-500 {{ $size === 'large' ? 'text-sm' : 'text-xs' }} flex items-center gap-1 hover:text-red-500 transition-colors"
                        >
                            @if($isFavorito)
                                <i class="bi bi-heart-fill text-red-500"></i>
                                <span class="{{ $size === 'large' ? 'text-sm' : 'text-xs' }}">Favorito</span>
                            @else
                                <i class="bi bi-heart"></i>
                                <span class="{{ $size === 'large' ? 'text-sm' : 'text-xs' }}">Favoritar</span>
                            @endif
                        </button>
                    @endauth

                    <span
                        class="bg-biblioteca-500 text-white px-3 py-1 rounded text-sm transition-colors group-hover:bg-biblioteca-600"
                    >
                        Detalhes
                    </span>
                </div>
            </div>
        </div>
    </a> </li>
