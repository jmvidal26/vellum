<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/css/splide.min.css">

<script src="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/js/splide.min.js"></script>

<div class="mx-auto" x-data="{ quizModalOpen: false }">
    <h2 class="text-3xl font-bold text-biblioteca-800 mb-6 text-center">
        Bem-vindo(a), {{ explode(' ', auth()->user()->name)[0] }}!
    </h2>
    <div class="bg-white rounded-xl shadow-md p-6 mb-8 border border-biblioteca-200 text-center">
        <p class="text-lg text-biblioteca-700 mb-4">Explore nosso acervo, participe de clubes do livro e encontre sua próxima grande leitura.</p>
        <p class="text-biblioteca-600">Comece navegando pelas seções abaixo ou use a barra de busca no menu para encontrar algo específico.</p>
    </div>

    <div class="flex flex-col md:flex-row justify-center gap-6 mb-12">
        <div class="bg-biblioteca-100 rounded-lg p-5 text-center shadow-sm hover:shadow-md transition-shadow md:w-72 flex flex-col justify-between">
            <div>
                <i class="bi bi-book text-4xl text-biblioteca-600 mb-3"></i>
                <h3 class="font-bold text-biblioteca-800 mb-2">Acervo Digital</h3>
                <div class="text-biblioteca-600 text-sm mb-4">
                    <p>Explore nossa coleção de</p>
                    <p class="text-2xl font-bold text-biblioteca-700 mt-1">
                        {{ \App\Models\Livro::count() }} obras
                    </p>
                </div>
            </div>
            <a href="{{ route('acervo') }}" class="inline-block bg-biblioteca-700 text-white px-8 py-2 rounded-lg font-medium hover:bg-biblioteca-800 transition-colors duration-300">
                Ir
            </a>
        </div>

        <div class="bg-gradient-to-br from-biblioteca-600 to-biblioteca-800 rounded-lg p-5 text-center shadow-lg hover:shadow-xl transition-shadow md:w-72 flex flex-col justify-between">
            <div>
                <i class="bi bi-patch-question-fill text-4xl text-white mb-3"></i>
                <h3 class="font-bold text-white text-xl mb-2">Quiz Literário</h3>
                <div class="text-biblioteca-200 text-sm mb-4">
                    <p>Desafio rápido!</p>
                    <p class="text-lg font-bold text-white mt-1">
                        Teste seus conhecimentos
                    </p>
                </div>
            </div>

            <button
                @click="quizModalOpen = true"
                type="button"
                class="inline-block bg-white text-biblioteca-700 px-8 py-2 rounded-lg font-medium hover:bg-biblioteca-100 transition-colors duration-300">
                Começar!
            </button>
        </div>

        <div class="bg-biblioteca-100 rounded-lg p-5 shadow-sm md:w-72 flex flex-col justify-between">
            <div class="text-center">
                <i class="bi bi-people text-4xl text-biblioteca-600 mb-3"></i>
                <h3 class="font-bold text-biblioteca-800 mb-2">Clube do Livro</h3>
                <div class="text-biblioteca-600 text-sm mb-4">
                    <p>Nosso livro deste mês:</p>

                    @if ($sessaoAtiva && $sessaoAtiva->livro)
                        <p class="text-2xl font-bold text-biblioteca-700 mt-1 truncate" title="{{ $sessaoAtiva->livro->titulo }}">
                            {{ $sessaoAtiva->livro->titulo }}
                        </p>
                    @else
                        <p class="text-lg font-medium text-biblioteca-500 mt-1">
                            Nenhum livro definido.
                        </p>
                    @endif

                </div>
            </div>

            <a href="{{ route('clube-do-livro') }}" class="text-center inline-block bg-biblioteca-700 text-white px-8 py-2 rounded-lg font-medium hover:bg-biblioteca-800 transition-colors duration-300">
                Participar
            </a>
        </div>
    </div>

    <div class="mt-12">
        <h3 id="downloads-title" class="text-2xl font-bold text-biblioteca-800 mb-6 flex items-center gap-2">
            <i class="bi bi-graph-up"></i>
            <span>Top Livros com + Downloads</span>
        </h3>
        <div wire:ignore>
            <section class="splide book-carousel" aria-labelledby="downloads-title">
                <div class="splide__track pt-4">
                    <ul class="splide__list">
                        @foreach($topDownloads as $livro)
                            <livewire:livro-card
                                :livro="$livro"
                                :key="$livro->id"
                            />
                        @endforeach
                    </ul>
                </div>
            </section>
        </div>
    </div>

    <div class="mt-12" x-data="tabsManager()">
        <h3 class="text-2xl font-bold text-biblioteca-800 mb-6 flex items-center gap-2">
            <i class="bi bi-tags"></i>
            <span>Explore por Gênero</span>
        </h3>

        <div class="flex flex-wrap justify-center border-b border-biblioteca-200 mb-6">

            <button @click="openTab('aventura', 'aventura-carousel')"
                    :class="{ 'border-biblioteca-700 text-biblioteca-800': tab === 'aventura' }"
                    class="py-2 px-4 -mb-px border-b-2 font-medium text-biblioteca-600 hover:text-biblioteca-800 focus:outline-none flex items-center gap-2">
                <i class="bi bi-compass"></i>
                <span>Aventura</span>
                <span class="ml-1 bg-biblioteca-100 text-biblioteca-700 text-xs font-semibold px-2 py-0.5 rounded-full">{{ $topAventuras->count() }}</span>
            </button>

            <button @click="openTab('romance', 'romance-carousel')"
                    :class="{ 'border-biblioteca-700 text-biblioteca-800': tab === 'romance' }"
                    class="py-2 px-4 -mb-px border-b-2 font-medium text-biblioteca-600 hover:text-biblioteca-800 focus:outline-none flex items-center gap-2">
                <i class="bi bi-heart"></i>
                <span>Romance</span>
                <span class="ml-1 bg-biblioteca-100 text-biblioteca-700 text-xs font-semibold px-2 py-0.5 rounded-full">{{ $topRomances->count() }}</span>
            </button>

            <button @click="openTab('fantasia', 'fantasia-carousel')"
                    :class="{ 'border-biblioteca-700 text-biblioteca-800': tab === 'fantasia' }"
                    class="py-2 px-4 -mb-px border-b-2 font-medium text-biblioteca-600 hover:text-biblioteca-800 focus:outline-none flex items-center gap-2">
                <i class="bi bi-magic"></i>
                <span>Fantasia</span>
                <span class="ml-1 bg-biblioteca-100 text-biblioteca-700 text-xs font-semibold px-2 py-0.5 rounded-full">{{ $topFantasias->count() }}</span>
            </button>

            <button @click="openTab('horror', 'horror-carousel')"
                    :class="{ 'border-biblioteca-700 text-biblioteca-800': tab === 'horror' }"
                    class="py-2 px-4 -mb-px border-b-2 font-medium text-biblioteca-600 hover:text-biblioteca-800 focus:outline-none flex items-center gap-2">
                <i class="bi bi-mask"></i>
                <span>Horror</span>
                <span class="ml-1 bg-biblioteca-100 text-biblioteca-700 text-xs font-semibold px-2 py-0.5 rounded-full">{{ $topHorror->count() }}</span>
            </button>

            <button @click="openTab('ficcao', 'ficcao-carousel')"
                    :class="{ 'border-biblioteca-700 text-biblioteca-800': tab === 'ficcao' }"
                    class="py-2 px-4 -mb-px border-b-2 font-medium text-biblioteca-600 hover:text-biblioteca-800 focus:outline-none flex items-center gap-2">
                <i class="bi bi-robot"></i>
                <span>Ficção</span>
                <span class="ml-1 bg-biblioteca-100 text-biblioteca-700 text-xs font-semibold px-2 py-0.5 rounded-full">{{ $topFiccao->count() }}</span>
            </button>

            <button @click="openTab('historia', 'historia-carousel')"
                    :class="{ 'border-biblioteca-700 text-biblioteca-800': tab === 'historia' }"
                    class="py-2 px-4 -mb-px border-b-2 font-medium text-biblioteca-600 hover:text-biblioteca-800 focus:outline-none flex items-center gap-2">
                <i class="bi bi-book-half"></i>
                <span>História</span>
                <span class="ml-1 bg-biblioteca-100 text-biblioteca-700 text-xs font-semibold px-2 py-0.5 rounded-full">{{ $topHistoria->count() }}</span>
            </button>

        </div>

        <div x-show="tab === 'aventura'" x-cloak>
            <section class="splide" id="aventura-carousel" aria-label="Aventura">
                <div class="splide__track pt-4"><ul class="splide__list">
                        @foreach($topAventuras as $livro) <livewire:livro-card :livro="$livro" :key="'av-'.$livro->id" /> @endforeach
                    </ul></div>
            </section>
        </div>
        <div x-show="tab === 'romance'" x-cloak>
            <section class="splide" id="romance-carousel" aria-label="Romance">
                <div class="splide__track pt-4"><ul class="splide__list">
                        @foreach($topRomances as $livro) <livewire:livro-card :livro="$livro" :key="'ro-'.$livro->id" /> @endforeach
                    </ul></div>
            </section>
        </div>
        <div x-show="tab === 'fantasia'" x-cloak>
            <section class="splide" id="fantasia-carousel" aria-label="Fantasia">
                <div class="splide__track pt-4"><ul class="splide__list">
                        @foreach($topFantasias as $livro) <livewire:livro-card :livro="$livro" :key="'fa-'.$livro->id" /> @endforeach
                    </ul></div>
            </section>
        </div>
        <div x-show="tab === 'horror'" x-cloak>
            <section class="splide" id="horror-carousel" aria-label="Horror">
                <div class="splide__track pt-4"><ul class="splide__list">
                        @foreach($topHorror as $livro) <livewire:livro-card :livro="$livro" :key="'ho-'.$livro->id" /> @endforeach
                    </ul></div>
            </section>
        </div>
        <div x-show="tab === 'ficcao'" x-cloak>
            <section class="splide" id="ficcao-carousel" aria-label="Ficção">
                <div class="splide__track pt-4"><ul class="splide__list">
                        @foreach($topFiccao as $livro) <livewire:livro-card :livro="$livro" :key="'fi-'.$livro->id" /> @endforeach
                    </ul></div>
            </section>
        </div>
        <div x-show="tab === 'historia'" x-cloak>
            <section class="splide" id="historia-carousel" aria-label="História">
                <div class="splide__track pt-4"><ul class="splide__list">
                        @foreach($topHistoria as $livro) <livewire:livro-card :livro="$livro" :key="'hi-'.$livro->id" /> @endforeach
                    </ul></div>
            </section>
        </div>
    </div>
    <div class="mx-auto">
        <div class="mt-12" x-data="tabsManager()">
        </div>
    </div>

    <livewire:livro-detalhes />
    <livewire:livro-texto />

    <div
        x-show="quizModalOpen"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
        x-cloak
    >
        <div
            @click.away="quizModalOpen = false"
            x-show="quizModalOpen"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="relative w-full max-w-2xl bg-white rounded-xl shadow-lg"
        >
            <div class="flex justify-between items-center p-4 border-b">
                <h3 class="text-xl font-bold text-biblioteca-800">
                    <i class="bi bi-patch-question-fill"></i>
                    Quiz Literário
                </h3>
                <button @click="quizModalOpen = false" class="text-biblioteca-500 hover:text-biblioteca-800">
                    <i class="bi bi-x-lg text-xl"></i>
                </button>
            </div>

            <div class="p-6 min-h-[300px]">

                <div x-if="quizModalOpen">
                    <livewire:literary-quiz wire:lazy />
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    var splideOptions = {
        type: 'slide',
        perPage: 7,
        gap: '0.5rem',
        pagination: false,
        arrows: 'true',
        breakpoints: {
            1024: { perPage: 5 },
            768: { perPage: 4 },
            640: { perPage: 3 },
        }
    };

    var mountedSplides = {};

    document.addEventListener('DOMContentLoaded', function () {
        var standardCarousels = document.querySelectorAll('.book-carousel');

        for (var i = 0; i < standardCarousels.length; i++) {
            var el = standardCarousels[i];
            var options = { ...splideOptions };

            new Splide(el, options).mount();
        }
    });

    function tabsManager() {
        return {
            tab: 'aventura',
            init() {
                this.mountSplide('aventura-carousel');
            },
            openTab(tabName, carouselId) {
                this.tab = tabName;
                setTimeout(() => this.mountSplide(carouselId), 10);
            },
            mountSplide(carouselId) {
                if (mountedSplides[carouselId]) {
                    return;
                }
                var element = document.getElementById(carouselId);
                if (element) {
                    var splide = new Splide(element, splideOptions);
                    splide.mount();
                    mountedSplides[carouselId] = true;
                }
            }
        }
    }
</script>
