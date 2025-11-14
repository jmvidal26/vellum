<?php

namespace App\Livewire;

use App\Models\Livro;
use App\Models\Colecao;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.menu')]
#[Title('Sua estante')]
class MinhaEstante extends Component
{
    use WithPagination;

    public $busca = '';
    public $ordenar = 'populares';

    public $colecoes;

    public $colecaoSelecionadaId = 'favoritos';
    public $tituloPagina = 'Favoritos';
    public $iconePagina = 'fa-solid fa-heart';
    public $corPagina = '#EF4444';

    public $novaPastaNome = '';
    public $novaPastaIcone = 'fa-solid fa-bookmark';
    public $novaPastaCor = '#6B7280';

    public $colecaoEditandoId = null;
    public $novoNomeColecao = '';
    public $novoIconeColecao = 'fa-solid fa-bookmark';
    public $novoIconeCor = '#6B7280';

    public $confirmandoExclusaoId = null;
    public $confirmandoExclusaoNome = '';


    public $iconList = [
        // --- Genéricos ---
        'fa-solid fa-book',             // Livro (Geral)
        'fa-solid fa-bookmark',         // Marcador (Padrão)
        'fa-solid fa-star',             // Favoritos / Destaque

        // --- Gêneros de Ficção ---
        'fa-solid fa-heart',            // Romance
        'fa-solid fa-wand-magic-sparkles', // Fantasia / Magia
        'fa-solid fa-user-astronaut',   // Ficção Científica
        'fa-solid fa-magnifying-glass', // Mistério / Suspense
        'fa-solid fa-ghost',            // Terror
        'fa-solid fa-scroll',           // Clássicos / Poesia

        // --- Gêneros de Não-Ficção ---
        'fa-solid fa-landmark',         // História
        'fa-solid fa-user-pen',         // Biografia / Memórias
        'fa-solid fa-brain',            // Autoajuda / Psicologia
        'fa-solid fa-flask-vial',       // Ciência
        'fa-solid fa-globe',            // Viagem / Geografia
        'fa-solid fa-utensils',         // Culinária
        'fa-solid fa-palette',          // Arte / Design
        'fa-solid fa-puzzle-piece',     // Infantil
    ];
    public $colorList = [
        '#6B7280', // Gray-500
        '#EF4444', // Red-500
        '#F97316', // Orange-500
        '#EAB308', // Yellow-500
        '#22C55E', // Green-500
        '#3B82F6', // Blue-500
        '#6366F1', // Indigo-500
        '#A855F7', // Purple-500
        '#EC4899', // Pink-500
    ];

    public function mount()
    {
        $this->colecoes = Auth::user()->colecoes()->orderBy('ordem')->get();
    }

    public function criarNovaColecao()
    {
        $this->validate([
            'novaPastaNome' => 'required|string|min:3|max:100',
            'novaPastaIcone' => 'required|string|in:' . implode(',', $this->iconList),
            'novaPastaCor' => 'required|string|in:' . implode(',', $this->colorList),
        ]);

        Auth::user()->colecoes()->create([
            'nome' => $this->novaPastaNome,
            'icone' => $this->novaPastaIcone,
            'icone_cor' => $this->novaPastaCor,
        ]);

        $this->reset('novaPastaNome', 'novaPastaIcone', 'novaPastaCor');
        $this->novaPastaIcone = 'fa-solid fa-bookmark';
        $this->novaPastaCor = '#6B7280';

        $this->colecoes = Auth::user()->colecoes()->orderBy('ordem')->get();
        $this->dispatch('coleacao-criada');
    }

    public function editarColecao($id)
    {
        $colecao = Colecao::find($id);

        if ($colecao) {
            $this->colecaoEditandoId = $id;
            $this->novoNomeColecao = $colecao->nome;
            $this->novoIconeColecao = $colecao->icone ?? 'fa-solid fa-bookmark';
            $this->novoIconeCor = $colecao->icone_cor ?? '#6B7280';
        }
    }

    public function salvarEdicaoColecao()
    {
        $this->validate([
            'novoNomeColecao' => 'required|string|min:3|max:100',
            'novoIconeColecao' => 'required|string|in:' . implode(',', $this->iconList),
            'novoIconeCor' => 'required|string|in:' . implode(',', $this->colorList),
        ]);

        $colecao = Colecao::find($this->colecaoEditandoId);
        if ($colecao && $colecao->user_id === Auth::id()) {
            $colecao->update([
                'nome' => $this->novoNomeColecao,
                'icone' => $this->novoIconeColecao,
                'icone_cor' => $this->novoIconeCor,
            ]);
        }

        $this->reset(['colecaoEditandoId', 'novoNomeColecao', 'novoIconeColecao', 'novoIconeCor']);
        $this->colecoes = Auth::user()->colecoes()->orderBy('ordem')->get();
    }

    public function confirmarExclusao($id)
    {
        $colecao = Colecao::find($id);
        if ($colecao && $colecao->user_id === Auth::id()) {
            $this->confirmandoExclusaoId = $colecao->id;
            $this->confirmandoExclusaoNome = $colecao->nome;
        }
    }

    public function cancelarExclusao()
    {
        $this->reset(['confirmandoExclusaoId', 'confirmandoExclusaoNome']);
    }

    public function excluirColecaoConfirmada()
    {
        $colecao = Colecao::find($this->confirmandoExclusaoId);
        if ($colecao && $colecao->user_id === Auth::id()) {
            $colecao->delete();
            $this->colecoes = Auth::user()->colecoes()->orderBy('ordem')->get();
        }

        $this->reset(['confirmandoExclusaoId', 'confirmandoExclusaoNome']);
    }

    public function selecionarColecao($id, $titulo, $icone, $cor)
    {
        $this->colecaoSelecionadaId = $id;
        $this->tituloPagina = $titulo;
        $this->iconePagina = $icone;
        $this->corPagina = $cor;

        usleep(100000);

        $this->resetPage();
    }

    public function updatedOrdenar()
    {
        $this->resetPage();
    }

    public function updatedBusca()
    {
        $this->resetPage();
    }

    public function render()
    {
        $userId = Auth::id();

        $query = Livro::query()->with('autores', 'formatos');

        $query->leftJoin('livro_avaliacoes', function ($join) use ($userId) {
            $join->on('livros.id', '=', 'livro_avaliacoes.livro_id')
                ->where('livro_avaliacoes.user_id', '=', $userId);
        });

        switch ($this->colecaoSelecionadaId) {
            case 'favoritos':
                $query->join('livros_favoritos', 'livros.id', '=', 'livros_favoritos.livro_id')
                    ->where('livros_favoritos.user_id', $userId);
                break;

            case 'em_andamento':
                $query->join('usuario_leituras', 'livros.id', '=', 'usuario_leituras.livro_id')
                    ->where('usuario_leituras.user_id', $userId)
                    ->where('usuario_leituras.status', 'lendo');
                break;

            default:
                $query->join('colecao_livro', 'livros.id', '=', 'colecao_livro.livro_id')
                    ->where('colecao_livro.colecao_id', $this->colecaoSelecionadaId);
                break;
        }

        if ($this->busca) {
            $query->where(function ($q) {
                $q->where('livros.titulo', 'like', '%' . $this->busca . '%')
                    ->orWhereHas('autores', function ($q_autor) {
                        $q_autor->where('nome', 'like', '%' . $this->busca . '%');
                    });
            });
        }

        if ($this->ordenar == 'populares') {
            $query->orderByDesc('livro_avaliacoes.rating');
        }
        elseif (is_numeric($this->ordenar)) {
            $query->where('livro_avaliacoes.rating', $this->ordenar);
        }

        $livros = $query->select('livros.*')
            ->paginate(20);

        return view('livewire.minha-estante', [
            'livros' => $livros,
        ]);
    }
}
