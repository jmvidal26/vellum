<?php

namespace App\Livewire;

use App\Models\Livro;
use App\Models\SearchTerm;
use Livewire\Component;
use Livewire\Attributes\On;

class ModalSearch extends Component
{
    public $isOpen = false;
    public string $busca = '';

    public $results = [];
    public $recentSearches = [];
    public $popularSearches = [];

    #[On('open-search-modal')]
    public function open()
    {
        $this->isOpen = true;
        $this->loadSearches();

        $this->dispatch('focus-search-input');
    }

    public function abrirDetalhesLivro($livroId)
    {
        $this->close();
        return redirect()->route('acervo', ['livroId' => $livroId]);
    }
    public function close()
    {
        $this->isOpen = false;
        $this->reset('busca', 'results');
    }
    public function loadSearches()
    {
        $this->recentSearches = array_slice(array_reverse(session('recent_searches', [])), 0, 5);

        $this->popularSearches = SearchTerm::orderBy('count', 'desc')
            ->limit(5)
            ->pluck('term');
    }
    public function updatedBusca($value)
    {
        if (strlen($value) < 3) {
            $this->results = [];
            return;
        }

        $this->results = Livro::where('titulo', 'like', '%' . $value . '%')
            ->orWhereHas('autores', fn($q) => $q->where('nome', 'like', '%' . $value . '%'))
            ->with('autores:id,nome')
            ->limit(5)
            ->get();
    }

    public function performSearch()
    {
        if (empty($this->busca)) {
            return;
        }

        $searches = session('recent_searches', []);
        array_unshift($searches, $this->busca);
        $searches = array_unique($searches);
        session(['recent_searches' => array_slice($searches, 0, 5)]);

        $term = SearchTerm::firstOrCreate(
            ['term' => strtolower($this->busca)],
            ['count' => 0]
        );
        $term->increment('count');

        $this->close();
        return redirect()->route('acervo', ['search' => $this->busca]);
    }
    public function searchFor($term)
    {
        $this->busca = $term;
        $this->performSearch();
    }

    public function render()
    {
        return view('livewire.modal-search');
    }
}
