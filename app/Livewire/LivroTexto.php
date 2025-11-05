<?php

namespace App\Livewire;

use App\Models\UsuarioLeitura;
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

    #[On('livroCarregado')]
    public function carregarConteudo(
        ?string $titulo = null,
        ?string $autores = null,
        ?string $capa = null,
        ?array $chapters = null,
        bool $isProse = true,
        ?int $livro_id = null
    ) {
        $this->titulo = $titulo ?? 'Erro ao Carregar';
        $this->autores = $autores ?? 'Desconhecido';
        $this->capaUrl = $capa;
        $this->chapters = $chapters ?? [['title' => 'Erro', 'content' => 'Ocorreu um erro inesperado ao carregar o conteúdo do livro.']];
        $this->isProse = $isProse;
        $this->livro_id = $livro_id;
        $this->mostrar = true;
        $this->dispatch('fechar-modal-detalhes');

        UsuarioLeitura::firstOrCreate([
           'livro_id' => $this->livro_id,
           'user_id' => auth()->id(),
        ]);

    }

    #[On('close-reader')]
    public function fechar()
    {
        $this->mostrar = false;
        $this->chapters = [];
        $this->titulo = '';
        $this->autores = '';
        $this->capaUrl = '';
        $this->livro_id = null;
        $this->dispatch('fechar-modal-detalhes');
    }

    public function render()
    {
        return view('livewire.livro-texto');
    }
}
