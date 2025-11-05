<?php

namespace App\Livewire;

use App\Models\Livro;
use App\Models\LivroAvaliacao;
use App\Models\LivroFavorito;
use Livewire\Component;
use Livewire\Attributes\On;

class LivroDetalhes extends Component
{
    public $livro;
    public $showModal = false;
    public $isFavorito = false;
    public $userRating = 0;

    public $mainGenres = [];
    public $allShelves = [];

    #[On('openLivroModal')]
    public function mostrarDetalhes($livroId)
    {
        $this->livro = Livro::with('autores', 'assuntos', 'estantes', 'formatos', 'avaliacoes')
            ->find($livroId);

        if (!$this->livro) {
            $this->closeModal();
            return;
        }

        $masterGenres = [
            'Aventura' => 'aventura', 'Romance' => 'romance', 'Fantasia' => 'fantasia',
            'Horror' => 'horror', 'Ficção' => 'ficção', 'História' => 'históri',
        ];
        $allShelvesRaw = $this->livro->estantes->pluck('nome');
        $foundGenres = [];
        $otherShelves = [];
        foreach ($allShelvesRaw as $shelf) {
            $found = false;
            foreach ($masterGenres as $cleanName => $keyword) {
                if (stripos($shelf, $keyword) !== false) {
                    $foundGenres[$cleanName] = true;
                    $found = true;
                }
            }
            if (!$found) {
                $shelf = str_replace('Categoria: ', '', $shelf);
                if (stripos($shelf, 'Best Books') === false && stripos($shelf, 'Banned Books') === false) {
                    $otherShelves[] = $shelf;
                }
            }
        }
        $this->mainGenres = array_keys($foundGenres);
        $this->allShelves = $otherShelves;


        if (auth()->check()) {
            $userId = auth()->id();
            $this->isFavorito = LivroFavorito::where('user_id', $userId)
                ->where('livro_id', $this->livro->id)
                ->exists();
            $this->userRating = LivroAvaliacao::where('user_id', $userId)
                ->where('livro_id', $this->livro->id)
                ->value('rating') ?? 0;
        }

        $this->showModal = true;
    }

    public function abrirLivro()
    {
        $url = null;
        $isHtml = false;

        $formato = $this->livro->formatos
            ->first(function ($formato) {
                return str_starts_with($formato->media_type, 'text/plain');
            });

        if ($formato) {
            $url = $formato->url;
        }

        if (!$url) {
            $formatoHtml = $this->livro->formatos
                ->first(function ($formato) {
                    return str_starts_with($formato->media_type, 'text/html');
                });

            if ($formatoHtml) {
                $url = $formatoHtml->url;
                $isHtml = true;
            }
        }

        if (!$url) {
            $formatoTxt = $this->livro->formatos
                ->first(function ($formato) {
                    return str_contains($formato->url, '.txt');
                });
            $url = $formatoTxt?->url;
        }

        $titulo = $this->livro->titulo ?? 'Não encontrado';
        $autores = 'Desconhecido';
        if ($this->livro->autores && $this->livro->autores->count() > 0) {
            $autores = $this->livro->autores->pluck('nome')->implode(', ');
        }
        $capa = $this->livro->formatos->firstWhere('media_type', 'image/jpeg')?->url;


        if ($url) {
            try {
                $conteudo = @file_get_contents($url);
                if ($conteudo === false) {
                    throw new \Exception("Não foi possível baixar o conteúdo da URL: $url");
                }

                $isProse = !preg_match('/(ACT [IVXLCDM]+|SCENE [IVXLCDM]+)/i', $conteudo);

                $conteudoSemiLimpo = '';
                if ($isHtml) {
                    $conteudoSemiLimpo = $this->limparHtmlParaTxt($conteudo);
                } else {
                    $conteudoSemiLimpo = $this->limparTextoGutenberg($conteudo);
                }

                $chapters = $this->parseChapters($conteudoSemiLimpo, $isProse);

                $this->dispatch('livroCarregado',
                    titulo: $titulo,
                    autores: $autores,
                    capa: $capa,
                    chapters: $chapters,
                    isProse: $isProse,
                    livro_id: $this->livro->id
                );

            } catch (\Exception $e) {
                $errorChapter = [['title' => 'Erro', 'content' => "Erro ao carregar o livro: " . $e->getMessage()]];
                $this->dispatch('livroCarregado',
                    titulo: $titulo,
                    autores: $autores,
                    capa: $capa,
                    chapters: $errorChapter,
                    isProse: true,
                    livro_id: $this->livro->id
                );
            }
        } else {
            $errorChapter = [['title' => 'Erro', 'content' => "Nenhuma versão de leitura (TXT ou HTML) foi encontrada para este livro."]];
            $this->dispatch('livroCarregado',
                titulo: $titulo,
                autores: $autores,
                capa: $capa,
                chapters: $errorChapter,
                isProse: true,
                livro_id: $this->livro->id
            );
        }
    }

    private function limparHtmlParaTxt($html)
    {
        if (function_exists('mb_convert_encoding')) {
            $html = mb_convert_encoding($html, 'UTF-8', 'UTF-8');
        }

        $html = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $html);
        $html = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', '', $html);
        $html = preg_replace('/<head\b[^>]*>(.*?)<\/head>/is', '', $html);

        if (preg_match('/<body[^>]*>(.*?)<\/body>/is', $html, $matches)) {
            $html = $matches[1];
        }

        $html = preg_replace('/<div class="pg-header"[^>]*>(.*?)<\/div>/is', '', $html);
        $html = preg_replace('/<div class="pg-footer"[^>]*>(.*?)<\/div>/is', '', $html);

        $html = preg_replace('/<p[^>]*>/i', "\n\n", $html);
        $html = preg_replace('/<br\s*\/?>/i', "\n", $html);

        $text = strip_tags($html);
        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');

        if (function_exists('mb_convert_encoding')) {
            $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8');
        }
        $text = preg_replace('/^\x{FEFF}/u', '', $text);
        $text = preg_replace('/^.*?\*\*\* START OF .*?\*\*\*/is', '', $text, 1);
        $text = preg_replace('/\*\*\* END OF .*?\*\*\*.*$/is', '', $text, 1);


        return trim($text);
    }

    private function limparTextoGutenberg($text)
    {
        if (function_exists('mb_convert_encoding')) {
            $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8');
        }

        $text = preg_replace('/^\x{FEFF}/u', '', $text);

        $text = preg_replace('/^.*?\*\*\* START OF .*?\*\*\*/is', '', $text, 1);

        $text = preg_replace('/\*\*\* END OF .*?\*\*\*.*$/is', '', $text, 1);


        return trim($text);
    }

    private function limparConteudoGutenberg($text)
    {
        return $this->limparTextoGutenberg($text);
    }

    private function parseChapters($text, $isProse)
    {
        $regex = '/^' .
            '(' .

            'PREFACE|CONTENTS|INTRODUCTION|FORWARD|EPILOGUE|ETYMOLOGY|EXTRACTS|Dramatis Personæ|THE PROLOGUE|PROLOGUE|CHORUS' .

            '|' .

            '(?:CHAPTER|BOOK|ACT|SCENE|PART|LETTER)\s+[IVXLCDM\d]+' .

            ')' .
            '.*$' .
            '/im';

        preg_match_all($regex, $text, $matches, PREG_OFFSET_CAPTURE);

        $chapters = [];

        $firstMatchOffset = $matches[0][0][1] ?? strlen($text);
        $introContent = substr($text, 0, $firstMatchOffset);

        if ($isProse) {
            $introContent = preg_replace('/([^\n])(\r\n|\r|\n)([^\n])/', '$1 $3', $introContent);
        }
        $introContent = preg_replace('/(\r\n|\r|\n){3,}/', "\n\n", $introContent); // Limpa quebras excessivas
        $introContent = trim($introContent);

        if (strlen($introContent) > 250) {
            $chapters[] = ['title' => 'Introdução', 'content' => $introContent];
        }

        foreach ($matches[0] as $index => $match) {
            $title = ucwords(strtolower(trim($match[0])));
            $title = rtrim($title, '.');

            $offset = $match[1];
            $contentStart = $offset + strlen($match[0]);
            $nextMatchOffset = $matches[0][$index + 1][1] ?? strlen($text);
            $contentLength = $nextMatchOffset - $contentStart;

            $content = substr($text, $contentStart, $contentLength);

            if ($isProse) {
                $content = preg_replace('/([^\n])(\r\n|\r|\n)([^\n])/', '$1 $3', $content);
            } else {
                $content = preg_replace('/(\r\n|\r|\n){3,}/', "\n\n", $content);
            }
            $content = trim($content);

            if (!empty($content)) {
                $chapters[] = ['title' => $title, 'content' => $content];
            }
        }

        if (empty($chapters) && !empty($introContent) && strlen($introContent) > 20) {
            if(count($chapters) == 0) {
                $chapters[] = ['title' => 'Livro Completo', 'content' => $introContent];
            }
        }

        return $chapters;
    }

    public function setRating($newRating)
    {
        if (!auth()->check() || !$this->livro) return;
        if ($newRating < 1 || $newRating > 5) return;

        $userId = auth()->id();
        $livroId = $this->livro->id;

        if ($this->userRating == $newRating) {
            LivroAvaliacao::where('user_id', $userId)
                ->where('livro_id', $livroId)
                ->delete();
            $this->userRating = 0;
            $this->livro->updateRating();
        } else {
            LivroAvaliacao::updateOrCreate(
                ['user_id' => $userId, 'livro_id' => $livroId],
                ['rating' => $newRating]
            );
            $this->userRating = $newRating;
        }
        $this->livro->refresh();

        $this->dispatch('rating-updated', rating: $this->userRating);
    }

    public function toggleFavorite()
    {
        if (!auth()->check() || !$this->livro) return;
        $query = LivroFavorito::where('user_id', auth()->id())->where('livro_id', $this->livro->id);
        if ($this->isFavorito) {
            $query->delete(); $this->isFavorito = false;
        } else {
            LivroFavorito::create(['user_id' => auth()->id(), 'livro_id' => $this->livro->id]);
            $this->isFavorito = true;
        }
        $this->dispatch('favoritoAtualizado');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->livro = null;
        $this->isFavorito = false;
        $this->userRating = 0;
        $this->mainGenres = [];
        $this->allShelves = [];
    }

    public function render()
    {
        return view('livewire.livro-detalhes');
    }
}
