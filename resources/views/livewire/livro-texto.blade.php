<div>
    @if($mostrar)
        <div
            x-data="{
                ...eReader({
                    chapters: {{ json_encode($chapters) }},
                    capa: {{ json_encode($capaUrl) }},
                    titulo: {{ json_encode($titulo) }},
                    autores: {{ json_encode($autores) }},
                    isProse: {{ json_encode($isProse) }}
                }),

                showUi: true,
                theme: localStorage.getItem('vellumReaderTheme') || 'light',
                fontFamily: localStorage.getItem('vellumReaderFontFamily') || 'serif'
            }"
            x-init="init()"
            x-cloak
            class="fixed inset-0 z-[60] flex items-center justify-center p-4 md:p-8"
        >
            <div class="fixed inset-0 bg-black bg-opacity-80" @click="fecharLeitor()"></div>

            <div
                class="bg-white rounded-xl shadow-xl w-full max-w-6xl h-full flex flex-col relative z-10 transition-colors duration-300"
                :class="{
                    'bg-white': theme === 'light',
                    'bg-sepia-50': theme === 'sepia',
                    'bg-gray-900': theme === 'dark'
                }"
            >

                <div
                    x-show="showUi || currentChapterIndex === 0"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="-translate-y-full opacity-0"
                    x-transition:enter-end="translate-y-0 opacity-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="translate-y-0 opacity-100"
                    x-transition:leave-end="-translate-y-full opacity-0"
                    class="flex justify-between items-center p-4 border-b transition-colors"
                    :class="{
        'border-biblioteca-200': theme === 'light',
        'border-sepia-800/20': theme === 'sepia',
        'border-gray-700': theme === 'dark'
    }"
                >
                    <div class="w-2/5 min-w-0">
                        <h2 class="text-lg font-bold truncate"
                            :class="{
                'text-biblioteca-800': theme === 'light',
                'text-sepia-800': theme === 'sepia',
                'text-gray-200': theme === 'dark'
            }"
                            x-text="titulo"></h2>
                        <p class="text-sm truncate"
                           :class="{
                'text-biblioteca-600': theme === 'light',
                'text-sepia-800/80': theme === 'sepia',
                'text-gray-400': theme === 'dark'
           }"
                           x-text="autores"></p>
                    </div>

                    <div class="flex-1 flex justify-center items-center gap-2 md:gap-4"
                         :class="{
                            'text-biblioteca-700': theme === 'light',
                            'text-sepia-800': theme === 'sepia',
                            'text-gray-400': theme === 'dark'
                         }">

                        <button @click="showToc = !showToc" class="p-2 rounded-lg"
                                :class="{
                                    'bg-biblioteca-100 text-biblioteca-700': showToc && theme === 'light',
                                    'hover:bg-biblioteca-100': theme === 'light',
                                    'bg-sepia-100 text-sepia-800': showToc && theme === 'sepia',
                                    'hover:bg-sepia-100': theme === 'sepia',
                                    'bg-gray-700 text-gray-200': showToc && theme === 'dark',
                                    'hover:bg-gray-700': theme === 'dark'
                                }" title="Sumário">
                            <i class="bi bi-list-nested text-xl"></i>
                        </button>

                        <button @click="fontFamily = (fontFamily === 'serif' ? 'sans' : 'serif')" class="p-2 rounded-lg"
                                :class="{
                                    'hover:bg-biblioteca-100': theme === 'light',
                                    'hover:bg-sepia-100': theme === 'sepia',
                                    'hover:bg-gray-700': theme === 'dark'
                                }" title="Mudar fonte">
                            <i class="bi bi-fonts text-xl" x-show="fontFamily === 'serif'"></i>
                            <i class="bi bi-type text-xl" x-show="fontFamily === 'sans'"></i>
                        </button>

                        <button @click="changeFontSize(-1)" class="p-2 rounded-lg" :class="{ 'hover:bg-biblioteca-100': theme === 'light', 'hover:bg-sepia-100': theme === 'sepia', 'hover:bg-gray-700': theme === 'dark' }" title="Diminuir fonte">
                            <i class="bi bi-fonts text-lg">A-</i>
                        </button>
                        <button @click="changeFontSize(1)" class="p-2 rounded-lg" :class="{ 'hover:bg-biblioteca-100': theme === 'light', 'hover:bg-sepia-100': theme === 'sepia', 'hover:bg-gray-700': theme === 'dark' }" title="Aumentar fonte">
                            <i class="bi bi-fonts text-xl">A+</i>
                        </button>

                        <div class="flex items-center border rounded-lg" :class="{ 'border-biblioteca-200': theme === 'light', 'border-sepia-800/20': theme === 'sepia', 'border-gray-700': theme === 'dark' }">
                            <button @click="theme = 'light'" class="p-2 rounded-l-lg" :class="{ 'bg-biblioteca-100': theme === 'light' }"><i class="bi bi-brightness-high"></i></button>
                            <button @click="theme = 'sepia'" class="p-2 border-l border-r" :class="{ 'bg-sepia-100': theme === 'sepia', 'border-sepia-800/20': theme === 'sepia', 'border-biblioteca-200': theme === 'light', 'border-gray-700': theme === 'dark' }"><i class="bi bi-egg"></i></button>
                            <button @click="theme = 'dark'" class="p-2 rounded-r-lg" :class="{ 'bg-gray-700 text-white': theme === 'dark' }"><i class="bi bi-moon-fill"></i></button>
                        </div>

                    </div>

                    <div class="w-auto flex justify-end">
                        <button @click="fecharLeitor()"
                                class="transition-colors"
                                :class="{
                                    'text-biblioteca-500 hover:text-biblioteca-700': theme === 'light',
                                    'text-sepia-800/60 hover:text-sepia-800': theme === 'sepia',
                                    'text-gray-500 hover:text-gray-300': theme === 'dark'
                                }">
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
                         class="w-full md:w-1/3 lg:w-1/4 border-r overflow-y-auto p-6"
                         style="display: none;"
                         :class="{
                            'bg-biblioteca-50 border-biblioteca-200': theme === 'light',
                            'bg-sepia-100 border-sepia-800/20': theme === 'sepia',
                            'bg-gray-800 border-gray-700': theme === 'dark'
                         }">

                        <h3 class="font-bold text-lg mb-4" :class="{ 'text-biblioteca-800': theme === 'light', 'text-sepia-800': theme === 'sepia', 'text-gray-200': theme === 'dark' }">Sumário</h3>
                        <ul class="space-y-2" :class="{ 'text-biblioteca-700': theme === 'light', 'text-sepia-900': theme === 'sepia', 'text-gray-300': theme === 'dark' }">
                            <li>
                                <button @click="goToChapter(0)" class="text-left" :class="{ 'font-bold text-biblioteca-900': currentChapterIndex === 0 && theme === 'light', 'font-bold text-sepia-900': currentChapterIndex === 0 && theme === 'sepia', 'font-bold text-white': currentChapterIndex === 0 && theme === 'dark', 'hover:text-biblioteca-900': theme === 'light', 'hover:text-sepia-900': theme === 'sepia', 'hover:text-white': theme === 'dark' }">
                                    Capa & Título
                                </button>
                            </li>
                            <template x-for="(chapter, index) in chapters" :key="index">
                                <li>
                                    <button @click="goToChapter(index + 1)"
                                            class="text-left"
                                            :class="{ 'font-bold text-biblioteca-900': currentChapterIndex === (index + 1) && theme === 'light', 'font-bold text-sepia-900': currentChapterIndex === (index + 1) && theme === 'sepia', 'font-bold text-white': currentChapterIndex === (index + 1) && theme === 'dark', 'hover:text-biblioteca-900': theme === 'light', 'hover:text-sepia-900': theme === 'sepia', 'hover:text-white': theme === 'dark' }">
                                        <span x-text="chapter.title"></span>
                                    </button>
                                </li>
                            </template>
                        </ul>
                    </nav>

                    <div class="flex-1 overflow-hidden relative" :class="showToc ? 'hidden md:block' : ''">
                        <div x-show="!showUi && currentChapterIndex > 0"
                             @click.stop="prevChapter()"
                             class="absolute left-0 top-0 h-full w-1/4 z-20 cursor-w-resize"
                             title="Capítulo Anterior"></div>
                        <div x-show="!showUi && currentChapterIndex > 0"
                             @click.stop="showUi = true"
                             class="absolute left-1/4 top-0 h-full w-1/2 z-10"
                             title="Mostrar UI"></div>
                        <div x-show="!showUi && currentChapterIndex < chapters.length"
                             @click.stop="nextChapter()"
                             class="absolute right-0 top-0 h-full w-1/4 z-20 cursor-e-resize"
                             title="Próximo Capítulo"></div>


                        <div class="h-full w-full flex flex-col p-4 md:p-8 lg:p-12"
                             @click.self="if (currentChapterIndex > 0) showUi = !showUi">
                            <div class="flex-1 overflow-y-auto" id="reader-content-area"
                                 :class="[
                                    fontSizeClass,
                                    fontFamily === 'serif' ? 'font-serif' : 'font-sans',
                                 ]">

                                <div x-show="currentChapterIndex === 0" class="flex flex-col items-center justify-center h-full text-center p-8">
                                    <img :src="capa || 'https://placehold.co/400x600/c08550/FFFFFF?text=Capa'" alt="Capa" class="w-56 aspect-[2/3] object-cover rounded-lg shadow-xl mb-8">
                                    <h1 class="text-4xl font-bold"
                                        :class="{ 'text-biblioteca-800': theme === 'light', 'text-sepia-800': theme === 'sepia', 'text-gray-100': theme === 'dark' }"
                                        x-text="titulo"></h1>
                                    <h2 class="text-2xl mt-2"
                                        :class="{ 'text-biblioteca-600': theme === 'light', 'text-sepia-800/80': theme === 'sepia', 'text-gray-400': theme === 'dark' }"
                                        x-text="autores"></h2>
                                </div>

                                <div x-show="currentChapterIndex > 0"
                                     class="md:columns-2 md:gap-x-12 lg:gap-x-16"
                                     :class="{
                                         'prose max-w-none text-justify': isProse,
                                         'max-w-none whitespace-pre-line': !isProse,

                                         'prose-invert': theme === 'dark' && isProse,
                                         'prose-sepia': theme === 'sepia' && isProse,

                                         'text-gray-900': theme === 'light' && !isProse,
                                         'text-sepia-900': theme === 'sepia' && !isProse,
                                         'text-gray-200': theme === 'dark' && !isProse
                                     }"
                                     x-html="chapters[currentChapterIndex - 1] ? chapters[currentChapterIndex - 1].content : ''">
                                </div>

                            </div>

                            <div
                                x-show="showUi || currentChapterIndex === 0"
                                x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="translate-y-full opacity-0"
                                x-transition:enter-end="translate-y-0 opacity-100"
                                x-transition:leave="transition ease-in duration-200"
                                x-transition:leave-start="translate-y-0 opacity-100"
                                x-transition:leave-end="translate-y-full opacity-0"
                                class="flex-shrink-0 flex flex-col pt-4 mt-4 border-t"
                                :class="{
                                    'border-biblioteca-200 text-biblioteca-600': theme === 'light',
                                    'border-sepia-800/20 text-sepia-800/80': theme === 'sepia',
                                    'border-gray-700 text-gray-400': theme === 'dark'
                                }"
                            >
                                <div class="w-full h-1.5 rounded-full mb-4" :class="{ 'bg-biblioteca-200': theme === 'light', 'bg-sepia-100': theme === 'sepia', 'bg-gray-700': theme === 'dark' }">
                                    <div class="h-1.5 rounded-full transition-all duration-300"
                                         :class="{ 'bg-biblioteca-700': theme === 'light', 'bg-sepia-800': theme === 'sepia', 'bg-gray-400': theme === 'dark' }"
                                         :style="{ width: `${((currentChapterIndex + 1) / (chapters.length + 1)) * 100}%` }">
                                    </div>
                                </div>

                                <div class="flex justify-between items-center">

                                    <button @click="prevChapter()" :disabled="currentChapterIndex === 0"
                                            class="px-4 py-2 rounded-lg font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                            :class="{
                                                'bg-biblioteca-200 text-biblioteca-700 hover:bg-biblioteca-300': theme === 'light',
                                                'bg-sepia-100 text-sepia-800 hover:bg-sepia-100/80': theme === 'sepia',
                                                'bg-gray-700 text-gray-300 hover:bg-gray-600': theme === 'dark'
                                            }">
                                        <i class="bi bi-arrow-left mr-2"></i> Anterior
                                    </button>

                                    <span class="text-sm font-medium">
                                        Página <span x-text="currentChapterIndex + 1"></span> de <span x-text="chapters.length + 1"></span>
                                    </span>

                                    <button @click="nextChapter()" :disabled="currentChapterIndex >= chapters.length"
                                            class="px-4 py-2 rounded-lg font-medium text-white transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                            :class="{
                                                'bg-biblioteca-700 hover:bg-biblioteca-800': theme === 'light',
                                                'bg-sepia-800 hover:bg-sepia-900': theme === 'sepia',
                                                'bg-gray-600 hover:bg-gray-500': theme === 'dark'
                                            }">
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
