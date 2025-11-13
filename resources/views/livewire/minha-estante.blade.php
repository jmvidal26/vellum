<div class="mx-auto">

    <div class="mb-10 text-center md:text-left">
        <h2 class="text-4xl font-bold text-biblioteca-800 mb-2">Sua Estante</h2>
        <p class="text-lg text-biblioteca-600">Organize e analise seus livros favoritos, em leitura e coleções.</p>
    </div>

    <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-8">
        <div class="relative w-full md:w-2/3 lg:w-1/2">
            <input type="search"
                   wire:model.debounce.500ms="busca"
                   placeholder="Pesquisar na coleção atual..."
                   class="w-full pl-10 pr-4 py-3 rounded-lg border border-biblioteca-300 focus:outline-none focus:ring-2 focus:ring-biblioteca-500">
            <i class="bi bi-search text-biblioteca-500 absolute left-3 top-1/2 -translate-y-1/2"></i>
        </div>
        <div class="flex-shrink-0 w-full md:w-auto">
            <select wire:model.defer="ordenar"
                    class="w-full md:w-auto p-3 rounded-lg border border-biblioteca-300 focus:outline-none focus:ring-2 focus:ring-biblioteca-500 bg-white">
                <option value="populares">⭐ Sua avaliação</option>
                <option value="1">★☆☆☆☆ 1 estrela</option>
                <option value="2">★★☆☆☆ 2 estrelas</option>
                <option value="3">★★★☆☆ 3 estrelas</option>
                <option value="4">★★★★☆ 4 estrelas</option>
                <option value="5">★★★★★ 5 estrelas</option>
            </select>
        </div>
    </div>

    <div class="flex flex-col md:flex-row gap-8 lg:gap-12">

        <aside class="md:w-1/4 lg:w-1/5 flex-shrink-0">

            <div x-data="{ criando: false, novaPastaNomeLocal: '' }" x-on:coleacao-criada.window="criando = false; novaPastaNomeLocal = ''">
                <div class="flex justify-between items-center mb-4 px-3">
                    <h3 class="text-lg font-bold text-biblioteca-800">Minhas Pastas</h3>
                    <button @click="criando = !criando"
                            class="flex items-center justify-center w-7 h-7 bg-biblioteca-100 text-biblioteca-600 rounded-full hover:bg-biblioteca-200 hover:text-biblioteca-800 transition-colors duration-200"
                            title="Criar nova pasta">
                        <i class="bi text-xl" :class="criando ? 'bi-x-lg' : 'bi-plus-lg'"></i>
                    </button>
                </div>

                <form
                    x-show="criando"
                    x-transition
                    x-cloak
                    class="mb-4 px-3 space-y-2"
                    @submit.prevent="
            // seta a propriedade Livewire com o valor local, chama o método e deixa o servidor responder
            $wire.set('novaPastaNome', novaPastaNomeLocal).then(() => $wire.call('criarNovaColecao'))
        "
                >
                    <input
                        type="text"
                        x-model="novaPastaNomeLocal"
                        placeholder="Nome da pasta..."
                        class="w-full text-sm rounded-md border-biblioteca-300 focus:ring-biblioteca-500"
                        aria-label="Nome da nova pasta"
                    >

                    <button
                        type="submit"
                        :disabled="novaPastaNomeLocal.length < 3"
                        class="w-full text-sm text-center bg-biblioteca-700 text-white rounded-md p-2 hover:bg-biblioteca-800 disabled:opacity-50"
                    >
                        <span wire:loading.remove wire:target="criarNovaColecao">Criar Pasta</span>
                        <span wire:loading wire:target="criarNovaColecao">Criando...</span>
                    </button>
                </form>
            </div>


            <nav class="space-y-1">
                <button
                    wire:click="selecionarColecao('favoritos', 'Favoritos', 'bi-heart-fill text-red-500')"
                    class="w-full flex items-center gap-3 px-3 py-2 rounded-lg font-medium text-left
                        {{ $colecaoSelecionadaId == 'favoritos'
                            ? 'bg-biblioteca-100 text-biblioteca-800'
                            : 'text-biblioteca-600 hover:bg-biblioteca-50' }}">
                    <i class="bi bi-heart-fill text-red-500 text-lg"></i>
                    <span>Favoritos</span>
                </button>

                <button
                    wire:click="selecionarColecao('em_andamento', 'Em Andamento', 'bi-book-half text-blue-600')"
                    class="w-full flex items-center gap-3 px-3 py-2 rounded-lg font-medium text-left
                        {{ $colecaoSelecionadaId == 'em_andamento'
                            ? 'bg-biblioteca-100 text-biblioteca-800'
                            : 'text-biblioteca-600 hover:bg-biblioteca-50' }}">
                    <i class="bi bi-book-half text-blue-600 text-lg"></i>
                    <span>Em Andamento</span>
                </button>

                <hr class="my-3 border-biblioteca-200">

                <div
                    id="lista-colecoes"
                    wire:sortable="atualizarOrdemColecoes"
                    class="space-y-1"
                    x-data="{ aberto: null }"
                >
                    @foreach($colecoes as $colecao)
                        <div
                            wire:key="colecao-{{ $colecao->id }}"
                            wire:sort.item="{{ $colecao->id }}"
                            class="relative flex items-center ..."
                        >
                            <div wire:sort.handle class="px-2 cursor-grab active:cursor-grabbing">
                                <i class="bi bi-grip-vertical text-biblioteca-400"></i>
                            </div>

                            <button
                                type="button"
                                wire:click="selecionarColecao({{ $colecao->id }}, '{{ addslashes($colecao->nome) }}', 'bi-bookmark-fill text-biblioteca-600')"
                                class="flex-1 text-left px-3 py-2 truncate
                                    {{ $colecaoSelecionadaId == $colecao->id
                                        ? 'bg-biblioteca-100 text-biblioteca-800'
                                        : 'text-biblioteca-600' }}"
                                title="{{ $colecao->nome }}"
                            >
                                <i class="bi bi-bookmark-fill text-biblioteca-600 text-lg mr-2"></i>
                                <span>{{ $colecao->nome }}</span>
                            </button>

                            <div class="relative">
                                <button
                                    @click.stop="aberto === {{ $colecao->id }} ? aberto = null : aberto = {{ $colecao->id }}"
                                    class="p-2 rounded hover:bg-biblioteca-200 transition"
                                    title="Mais ações"
                                >
                                    <i class="bi bi-three-dots-vertical text-biblioteca-600"></i>
                                </button>

                                <div
                                    x-show="aberto === {{ $colecao->id }}"
                                    x-transition.opacity.duration.150ms
                                    @click.away="aberto = null"
                                    class="absolute right-0 mt-2 w-36 bg-white border border-biblioteca-200 rounded-lg shadow-md z-20"
                                    x-cloak
                                >
                                    <button
                                        type="button"
                                        wire:click="editarColecao({{ $colecao->id }})"
                                        @click.stop="aberto = null"
                                        class="w-full text-left px-3 py-2 text-sm hover:bg-biblioteca-100"
                                    >
                                        ✏️ Editar
                                    </button>
                                    <button
                                        type="button"
                                        wire:click="confirmarExclusao({{ $colecao->id }})"
                                        @click.stop="aberto = null"
                                        class="w-full text-left px-3 py-2 text-sm text-red-600 hover:bg-red-50"
                                    >
                                        🗑️ Excluir
                                    </button>

                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </nav>

            @if($colecaoEditandoId)
                <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50" wire:key="modal-editar-colecao">
                    <div class="bg-white rounded-xl shadow-lg p-6 w-96">
                        <h2 class="text-lg font-semibold mb-4">Editar Coleção</h2>
                        <input type="text"
                               wire:model="novoNomeColecao"
                               class="w-full border rounded-md p-2 mb-4 focus:ring-biblioteca-500"
                               placeholder="Novo nome da coleção">
                        <div class="flex justify-end gap-2">
                            <button wire:click="$set('colecaoEditandoId', null)"
                                    class="px-4 py-2 rounded-md bg-gray-200 hover:bg-gray-300">Cancelar</button>
                            <button wire:click="salvarEdicaoColecao"
                                    class="px-4 py-2 rounded-md bg-biblioteca-700 text-white hover:bg-biblioteca-800">Salvar</button>
                        </div>
                    </div>
                </div>
            @endif

        </aside>

        @if($confirmandoExclusaoId)
            <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
                 wire:key="modal-confirmar-exclusao">
                <div class="bg-white rounded-xl shadow-lg p-6 w-96 text-center">
                    <h2 class="text-lg font-semibold mb-4 text-biblioteca-800">
                        Excluir coleção
                    </h2>

                    <p class="text-biblioteca-700 mb-6">
                        Tem certeza de que deseja excluir a coleção<br>
                        <span class="block font-semibold text-biblioteca-900 text-lg mt-1">
                    “{{ $confirmandoExclusaoNome }}”?
                </span>
                        <br>
                        Essa ação não poderá ser desfeita.
                    </p>

                    <div class="flex justify-center gap-4">
                        <button wire:click="cancelarExclusao"
                                class="px-4 py-2 rounded-md bg-gray-200 hover:bg-gray-300 transition">
                            Cancelar
                        </button>

                        <button wire:click="excluirColecaoConfirmada"
                                class="px-4 py-2 rounded-md bg-red-600 text-white hover:bg-red-700 transition">
                            Excluir
                        </button>
                    </div>
                </div>
            </div>
        @endif



        <main class="flex-1 min-w-0">

            <div class="hidden w-full mb-6 justify-center items-center gap-3 bg-biblioteca-100 p-4 rounded-lg"
                 wire:loading.delay.flex
                 wire:target="selecionarColecao, updatedOrdenar, updatedBusca"
            >
                <i class="bi bi-arrow-clockwise text-2xl text-biblioteca-700 animate-spin"></i>
                <span class="font-medium text-biblioteca-700">Atualizando estante...</span>
            </div>

            <div class="flex items-center gap-2 mb-4">
                <i class="bi {{ $iconePagina }} text-xl"></i>
                <h1 class="text-2xl font-semibold text-biblioteca-800">{{ $tituloPagina }}</h1>
            </div>

            <div
                wire:loading.remove.delay
                wire:target="selecionarColecao, updatedOrdenar, updatedBusca"
                class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6"
            >
                @forelse($livros as $livro)
                    <livewire:livro-card :livro="$livro" :key="$livro->id . $colecaoSelecionadaId" size="large" />
                @empty
                    <div class="col-span-full text-center py-12 bg-white border border-biblioteca-200 rounded-lg shadow-sm">
                        <i class="bi bi-search-heart text-5xl text-biblioteca-400 mb-4"></i>
                        <h4 class="text-xl font-bold text-biblioteca-700">Nenhum livro encontrado</h4>
                        <p class="text-biblioteca-600">Tente ajustar seus termos de busca ou filtros.</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-8">{{ $livros->links() }}</div>
        </main>
    </div>

    <livewire:livro-detalhes />

</div>

