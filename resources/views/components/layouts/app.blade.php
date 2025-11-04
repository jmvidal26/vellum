<!DOCTYPE html>
<html lang="pt-BR">
<head>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Vellum' }}</title>

    <link rel="icon" href="{{ asset('imagens/logo_icon_branco.png') }}" type="image/png">
    <link rel="apple-touch-icon" href="{{ asset('imagens/logo_icon_branco.png') }}">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css" />

    @livewireStyles

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Georgia', serif; }
        .logo-text { font-family: 'Georgia', serif; text-shadow: 1px 1px 2px rgba(0,0,0,0.1); }
        .nav-link { position: relative; transition: all 0.3s ease; }
        .nav-link:after { content: ''; position: absolute; width: 0; height: 2px; bottom: -5px; left: 0; background-color: #d2a274; transition: width 0.3s ease; }
        .nav-link:hover:after, .active-nav:after { width: 100%; }

        .img-container {
            width: 100%;
            height: 400px;
            background-color: #f7f7f7;
        }
        .img-container img {
            display: block;
            max-width: 100%;
        }
    </style>

    @livewireScripts
</head>
<body class="bg-biblioteca-50 text-biblioteca-900 min-h-screen flex flex-col">

{{ $slot }}

<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>

<script>
    function eReader(data) {
        return {
            rawContent: data.rawContent || '',
            capa: data.capa,
            titulo: data.titulo || 'Título Desconhecido',
            autores: data.autores || 'Autor Desconhecido',
            isProse: data.isProse !== undefined ? data.isProse : true,

            chapters: [],
            currentChapterIndex: 0,
            showToc: false,
            fontSizeIndex: 2,
            fontSizes: ['text-sm', 'text-base', 'text-lg', 'text-xl', 'text-2xl'],
            fontSizeClass: 'text-lg',

            init() {
                this.parseChapters();
                document.body.classList.add('overflow-hidden');

                this.scrollToTop();
            },

            fecharLeitor() {
                window.dispatchEvent(new CustomEvent('close-reader'));
                document.body.classList.remove('overflow-hidden');
            },

            parseChapters() {

                const chapterRegex = /^(CONTENTS|THE INTRODUCTION|THE PROLOGUE|PROLOGUE|Dramatis Personæ|ACT (?:[IVXLCDM]+|\d+)\.?.+|SCENE \w+\.?.+|CHAPTER \w+\.?.+|PART \w+\.?.+|[A-Z\s]{5,})$/m;

                let splits = this.rawContent.split(chapterRegex);
                let chapters = [];

                const minChapterLength = 100;

                if (splits.length > 1) {

                    if (splits[0].trim().length > minChapterLength) {
                        chapters.push({
                            title: 'Prefácio',
                            content: splits[0].trim()
                        });
                    }

                    for (let i = 1; i < splits.length; i += 2) {
                        const title = splits[i].trim().replace(/\r\n/g, ' ').replace(/\n/g, ' ');
                        const content = splits[i + 1].trim();

                        if (content.length > minChapterLength) {
                            chapters.push({
                                title: title,
                                content: content
                            });
                        }

                        else if (chapters.length > 0 && content.length > 0) {
                            chapters[chapters.length - 1].content += '\n\n' + title + '\n\n' + content;
                        }
                    }
                }

                if (chapters.length === 0) {
                    this.chapters = [{
                        title: 'Início',
                        content: this.rawContent.trim()
                    }];
                } else {
                    this.chapters = chapters;
                }
            },

            goToChapter(index) {
                this.currentChapterIndex = index;
                this.showToc = false;
                this.scrollToTop();
            },
            nextChapter() {
                if (this.currentChapterIndex < this.chapters.length) {
                    this.currentChapterIndex++;
                    this.scrollToTop();
                }
            },
            prevChapter() {
                if (this.currentChapterIndex > 0) {
                    this.currentChapterIndex--;
                    this.scrollToTop();
                }
            },
            scrollToTop() {
                this.$nextTick(() => {
                    const contentArea = document.getElementById('reader-content-area');
                    if (contentArea) contentArea.scrollTop = 0;
                });
            },

            changeFontSize(direction) {
                let newIndex = this.fontSizeIndex + direction;
                if (newIndex >= 0 && newIndex < this.fontSizes.length) {
                    this.fontSizeIndex = newIndex;
                    this.fontSizeClass = this.fontSizes[newIndex];
                }
            }
        }
    }
</script>

</body>
</html>
