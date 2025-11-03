<?php

namespace App\Livewire;

use App\Models\Livro;
use App\Models\LivroAvaliacao;
use App\Models\LivroFavorito;
use App\Services\CommumFunctions;
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

    public $conteudoLivro;

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
            'Aventura' => 'aventura',
            'Romance' => 'romance',
            'Fantasia' => 'fantasia',
            'Horror' => 'horror',
            'Ficção' => 'ficção',
            'História' => 'históri',
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
        $url = $this->livro->formatos
            ->where('media_type', 'text/html')
            ->first()?->url;
        if ($url) {
            try {
                $conteudoHtml = file_get_contents($url);
                $this->conteudoLivro = $conteudoHtml;

                $this->dispatch('livroCarregado', conteudo: $this->conteudoLivro);

            } catch (\Exception $e) {
                $this->conteudoLivro = "Erro ao carregar o livro: " . $e->getMessage();
            }
        } else {
            $this->conteudoLivro = "URL do livro não encontrada.";
        }
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
