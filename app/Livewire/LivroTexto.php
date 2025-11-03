<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;

class LivroTexto extends Component
{
    public $conteudoLivro = '';
    public $mostrar = false;

    #[On('livroCarregado')]
    public function carregarConteudo($conteudo)
    {
        $this->conteudoLivro = $conteudo;
        $this->mostrar = true;
    }

    public function fechar()
    {
        $this->mostrar = false;
        $this->conteudoLivro = '';
    }
    public function limparConteudo($html)
    {
        $html = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $html);
        $html = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', '', $html);

        $html = preg_replace('/<head\b[^>]*>(.*?)<\/head>/is', '', $html);

        if (preg_match('/<body[^>]*>(.*?)<\/body>/is', $html, $matches)) {
            $html = $matches[1];
        }

        return $html;
    }

    public function render()
    {
        return view('livewire.livro-texto');
    }
}
