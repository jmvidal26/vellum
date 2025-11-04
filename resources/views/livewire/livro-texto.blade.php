<div>
    @if($mostrar)
        <div
            x-data="eReader({
                rawContent: {{ json_encode($conteudoLivro) }},
                capa: {{ json_encode($capaUrl) }},
                titulo: {{ json_encode($titulo) }},
                autores: {{ json_encode($autores) }},
                isProse: {{ json_encode($isProse) }}
            })"
            x-init="init()"
            x-cloak
            class="fixed inset-0 z-[60] flex items-center justify-center p-4 md:p-8"
        >
            <div class="fixed inset-0 bg-black bg-opacity-80" @click="fecharLeitor()"></div>

            <div class="bg-white rounded-xl shadow-xl w-full max-w-6xl h-full flex flex-col relative z-10">

                <div class="flex justify-between items-center p-4 border-b border-biblioteca-200 flex-shrink-0">
                    <div class="w-1/3">
                        <h2 class="text-lg font-bold text-biblioteca-800 truncate" x-text="titulo"></h2>
                        <p class="text-sm text-biblioteca-600 truncate" x-text="autores"></p>
                    </div>

                    <div class="flex-1 flex justify-center items-center gap-4">
                        <button @click="showToc = !showToc"
                                class="p-2 rounded-lg hover:bg-biblioteca-100"
                                :class="{ 'bg-biblioteca-100 text-biblioteca-700': showToc }"
                                title="Sumário">
                            <i class="bi bi-list-nested text-xl"></i>
                        </button>
                        <button @click="changeFontSize(-1)" class="p-2 rounded-lg hover:bg-biblioteca-100" title="Diminuir fonte">
                            <i class="bi bi-fonts text-lg">A-</i>
                        </button>
                        <button @click="changeFontSize(1)" class="p-2 rounded-lg hover:bg-biblioteca-100" title="Aumentar fonte">
                            <i class="bi bi-fonts text-xl">A+</i>
                        </button>
                    </div>

                    <div class="w-1/3 flex justify-end">
                        <button @click="fecharLeitor()" class="text-biblioteca-500 hover:text-biblioteca-700 transition-colors">
                            <i class="bi bi-x-lg text-2xl"></i>
                        </button>
                    </div>
                </div>

                <div class="flex-1 flex overflow-hidden">

                    <nav x-show="showToc"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="-translate-x-full"
                         x-transition:enter-end="translate-x-0"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="translate-x-0"
                         x-transition:leave-end="-translate-x-full"
                         class="w-full md:w-1/3 lg:w-1/4 bg-biblioteca-50 border-r border-biblioteca-200 overflow-y-auto p-6"
                         style="display: none;">
                        <h3 class="font-bold text-biblioteca-800 text-lg mb-4">Sumário</h3>
                        <ul class="space-y-2">
                            <li>
                                <button @click="goToChapter(0)" class="text-left text-biblioteca-700 hover:text-biblioteca-900" :class="{ 'font-bold text-biblioteca-900': currentChapterIndex === 0 }">
                                    Capa & Título
                                </button>
                            </li>
                            <template x-for="(chapter, index) in chapters" :key="index">
                                <li>
                                    <button @click="goToChapter(index + 1)"
                                            class="text-left text-biblioteca-700 hover:text-biblioteca-900"
                                            :class="{ 'font-bold text-biblioteca-900': currentChapterIndex === (index + 1) }">
                                        <span x-text="chapter.title"></span>
                                    </button>
                                </li>
                            </template>
                        </ul>
                    </nav>

                    <div class="flex-1 overflow-hidden p-4 md:p-8" :class="showToc ? 'hidden md:block' : ''">
                        <div class="h-full w-full flex flex-col">

                            <div class="flex-1 overflow-y-auto" id="reader-content-area"
                                 :class="fontSizeClass"
                                 style="font-family: 'Georgia', serif;">

                                <div x-show="currentChapterIndex === 0" class="flex flex-col items-center justify-center h-full text-center p-8">
                                    <img :src="capa || 'https://placehold.co/400x600/c08550/FFFFFF?text=Capa'" alt="Capa" class="w-56 aspect-[2/3] object-cover rounded-lg shadow-xl mb-8">
                                    <h1 class="text-4xl font-bold text-biblioteca-800" x-text="titulo"></h1>
                                    <h2 class="text-2xl text-biblioteca-600 mt-2" x-text="autores"></h2>
                                </div>

                                <div x-show="currentChapterIndex > 0"
                                     class="prose max-w-none"
                                     :class="{
                                         'whitespace-pre-line': !isProse,
                                         'text-justify': isProse
                                     }"
                                     x-html="chapters[currentChapterIndex - 1] ? chapters[currentChapterIndex - 1].content : ''">
                                </div>

                            </div>
                            <div class="flex-shrink-0 flex justify-between items-center pt-4 mt-4 border-t border-biblioteca-200">
                                <button @click="prevChapter()" :disabled="currentChapterIndex === 0"
                                        class="px-4 py-2 rounded-lg font-medium bg-biblioteca-200 text-biblioteca-700 hover:bg-biblioteca-300 disabled:opacity-50 disabled:cursor-not-allowed">
                                    <i class="bi bi-arrow-left mr-2"></i> Anterior
                                </button>

                                <span class="text-biblioteca-600 text-sm font-medium">
                                    Página <span x-text="currentChapterIndex + 1"></span> de <span x-text="chapters.length + 1"></span>
                                </span>

                                <button @click="nextChapter()" :disabled="currentChapterIndex >= chapters.length"
                                        class="px-4 py-2 rounded-lg font-medium bg-biblioteca-700 text-white hover:bg-biblioteca-800 disabled:opacity-50 disabled:cursor-not-allowed">
                                    Seguinte <i class="bi bi-arrow-right ml-2"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    @endif
</div>
