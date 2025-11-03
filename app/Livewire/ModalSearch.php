<?php

namespace App\Livewire;

use App\Models\Livro;
use App\Models\SearchTerm;
use Livewire\Component;
use Livewire\Attributes\On;

class ModalSearch extends Component
{
    public bool $isOpen = false;
    public string $busca = '';

    public $results = [];
    public $recentSearches = [];
    public $popularSearches = [];

    /**
     * Ouve o evento global 'open-search-modal' disparado pelo Alpine.js no layout
     */
    public function open()
    {
        $this->isOpen = true;
        $this->loadSearches(); // Carrega buscas recentes/populares ao abrir

        // Dispara um evento para o Alpine focar no input
        $this->dispatch('focus-search-input');
    }

    /**
     * Fecha o modal e reseta a busca
     */
    public function close()
    {
        $this->isOpen = false;
        $this->reset('busca', 'results');
    }

    /**
     * Carrega as buscas da sessão e do banco
     */
    public function loadSearches()
    {
        // Pega as 5 buscas mais recentes da sessão
        $this->recentSearches = array_slice(array_reverse(session('recent_searches', [])), 0, 5);

        // Pega os 5 termos mais populares do banco
        $this->popularSearches = SearchTerm::orderBy('count', 'desc')
            ->limit(5)
            ->pluck('term');
    }

    /**
     * Executado automaticamente quando a propriedade $busca é atualizada
     * (graças ao wire:model.live)
     */
    public function updatedBusca($value)
    {
        // Só busca se tiver 3+ caracteres
        if (strlen($value) < 3) {
            $this->results = [];
            return;
        }

        // Faz a busca "live" no banco
        $this->results = Livro::where('titulo', 'like', '%' . $value . '%')
            ->orWhereHas('autores', fn($q) => $q->where('nome', 'like', '%' . $value . '%'))
            ->with('autores:id,nome') // Carrega autores para exibição
            ->limit(5) // Limita a 5 resultados no modal
            ->get();
    }

    /**
     * Chamado ao submeter o formulário (pressionar Enter ou clicar em Buscar)
     */
    public function performSearch()
    {
        if (empty($this->busca)) {
            return;
        }

        // 1. Salva na sessão (Buscas Recentes)
        $searches = session('recent_searches', []);
        array_unshift($searches, $this->busca); // Adiciona no início
        $searches = array_unique($searches); // Remove duplicados
        session(['recent_searches' => array_slice($searches, 0, 5)]); // Salva os 5 últimos

        // 2. Salva no banco (Buscas Populares)
        $term = SearchTerm::firstOrCreate(
            ['term' => strtolower($this->busca)],
            ['count' => 0]
        );
        $term->increment('count');

        // 3. Fecha o modal e redireciona para a página de acervo
        $this->close();
        return redirect()->route('acervo', ['search' => $this->busca]);
    }

    /**
     * Usado pelos links de busca recente/popular
     */
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
