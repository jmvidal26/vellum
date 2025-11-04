<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;

class LivroTexto extends Component
{
    public $conteudoLivro = '';
    public $titulo = '';
    public $autores = '';
    public $capaUrl = '';
    public $mostrar = false;
    public $isProse = true;

    #[On('livroCarregado')]
    public function carregarConteudo(
        ?string $titulo = null,
        ?string $autores = null,
        ?string $capa = null,
        ?string $conteudo = null,
        bool $isProse = true
    ) {
        $this->titulo = $titulo ?? 'Erro ao Carregar';
        $this->autores = $autores ?? 'Desconhecido';
        $this->capaUrl = $capa;
        $this->conteudoLivro = $conteudo ?? 'Ocorreu um erro inesperado ao carregar o conteúdo do livro.';
        $this->isProse = $isProse;
        $this->mostrar = true;
    }

    #[On('close-reader')]
    public function fechar()
    {
        $this->mostrar = false;
        $this->conteudoLivro = '';
        $this->titulo = '';
        $this->autores = '';
        $this->capaUrl = '';
        $this->dispatch('fechar-modal-detalhes');
    }

    public function render()
    {
        return view('livewire.livro-texto');
    }
}
