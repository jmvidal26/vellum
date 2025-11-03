<div>
    @if($mostrar)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-6xl h-5/6 flex flex-col">
                <div class="flex justify-between items-center p-4 border-b">
                    <h2 class="text-xl font-semibold">Leitor de Livro</h2>
                    <button
                        wire:click="fechar"
                        class="text-gray-500 hover:text-gray-700 text-2xl"
                    >
                        &times;
                    </button>
                </div>

                <div class="flex-1 overflow-auto p-6">
                    @if($conteudoLivro)
                        <div class="prose max-w-none">
                            {!! $this->limparConteudo($conteudoLivro) !!}
                        </div>
                    @else
                        <div class="text-center text-gray-500">
                            Nenhum conteúdo carregado.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
