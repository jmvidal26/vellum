<?php

namespace App\Livewire;

use App\Models\UsuarioLeitura;
use App\Models\User;
use App\Models\Livro;
use App\Services\BadgeService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\On;

class LivroTexto extends Component
{
    public $chapters = [];
    public $titulo = '';
    public $autores = '';
    public $capaUrl = '';
    public $mostrar = false;

    public $livro_id;
    public $isProse = true;

    public $progressoInicial = 0;

    #[On('carregarLivroPeloId')]
    public function carregarLivro(int $livroId)
    {
        $livro = Livro::with('autores', 'formatos')->find($livroId);

        if (!$livro) {
            $this->mostrar = true;
            $this->chapters = [['title' => 'Erro', 'content' => 'Não foi possível encontrar o livro.']];
            return;
        }

        $this->livro_id = $livroId;

        $url = null;
        $isHtml = false;
        $formato = $livro->formatos->first(fn ($f) => str_starts_with($f->media_type, 'text/plain'));
        if ($formato) $url = $formato->url;
        if (!$url) {
            $formatoHtml = $livro->formatos->first(fn ($f) => str_starts_with($f->media_type, 'text/html'));
            if ($formatoHtml) {
                $url = $formatoHtml->url;
                $isHtml = true;
            }
        }
        if (!$url) {
            $formatoTxt = $livro->formatos->first(fn ($f) => str_contains($f->url, '.txt'));
            $url = $formatoTxt?->url;
        }

        $this->titulo = $livro->titulo ?? 'Não encontrado';
        $this->autores = 'Desconhecido';
        if ($livro->autores && $livro->autores->count() > 0) {
            $this->autores = $livro->autores->pluck('nome')->implode(', ');
        }
        $this->capaUrl = $livro->formatos->firstWhere('media_type', 'image/jpeg')?->url;

        $this->progressoInicial = 0;
        if (Auth::check()) {
            $leitura = UsuarioLeitura::firstWhere([
                'user_id' => Auth::id(),
                'livro_id' => $livroId
            ]);
            if ($leitura && $leitura->status != 'finalizado') {
                $this->progressoInicial = $leitura->progresso_leitura;
            }
        }

        if ($url) {
            try {
                $conteudo = @file_get_contents($url);
                if ($conteudo === false) throw new \Exception("Não foi possível baixar o conteúdo.");

                $this->isProse = !preg_match('/(ACT [IVXLCDM]+|SCENE [IVXLCDM]+)/i', $conteudo);
                $conteudoSemiLimpo = $isHtml ? $this->limparHtmlParaTxt($conteudo) : $this->limparTextoGutenberg($conteudo);
                $this->chapters = $this->parseChapters($conteudoSemiLimpo, $this->isProse);

            } catch (\Exception $e) {
                $this->chapters = [['title' => 'Erro', 'content' => "Erro ao carregar o livro: " . $e->getMessage()]];
            }
        } else {
            $this->chapters = [['title' => 'Erro', 'content' => "Nenhuma versão de leitura (TXT ou HTML) foi encontrada."]];
        }

        $this->mostrar = true;
        $this->dispatch('fechar-modal-detalhes');
    }

    public function salvarEFechar($progressoAtual)
    {
        if (Auth::check() && $this->livro_id) {

            $leitura = UsuarioLeitura::firstOrNew(
                [
                    'user_id' => Auth::id(),
                    'livro_id' => $this->livro_id
                ]
            );

            if ($leitura->status != 'finalizado') {
                $leitura->progresso_leitura = $progressoAtual;
                $leitura->status = 'lendo';
                $leitura->save();
            }
        }

        $this->fecharLeitor();
    }

    public function finalizarLivro()
    {
        if (Auth::check() && $this->livro_id) {

            UsuarioLeitura::updateOrCreate(
                [
                    'user_id' => Auth::id(),
                    'livro_id' => $this->livro_id
                ],
                [
                    'progresso_leitura' => count($this->chapters),
                    'status' => 'finalizado'
                ]
            );

            $user = User::find(Auth::id());
            if ($user && isset($user->livros_lidos)) {
                $user->increment('livros_lidos');
            }

            BadgeService::verificarEmblemas(Auth::user(), 'livros_finalizados');

            $this->dispatch('livroFinalizado');
        }

        $this->fecharLeitor();
    }

    public function fecharLeitor()
    {
        $this->mostrar = false;
        $this->reset();
        $this->dispatch('fechar-modal-detalhes');
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
        $introContent = preg_replace('/(\r\n|\r|\n){3,}/', "\n\n", $introContent);
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

    public function render()
    {
        return view('livewire.livro-texto');
    }
}
