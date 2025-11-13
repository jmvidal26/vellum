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
    public $iconePagina = 'bi-heart-fill text-red-500';
    public $novaPastaNome = '';
    public $colecaoEditandoId = null;
    public $novoNomeColecao = '';
    public $confirmandoExclusaoId = null;
    public $confirmandoExclusaoNome = '';

    public function mount()
    {
        $this->colecoes = Auth::user()->colecoes()->orderBy('ordem')->get();
    }

    public function criarNovaColecao()
    {
        $this->validate([
            'novaPastaNome' => 'required|string|min:3|max:100'
        ]);

        Auth::user()->colecoes()->create([
            'nome' => $this->novaPastaNome
        ]);

        $this->reset('novaPastaNome');

        $this->colecoes = Auth::user()->colecoes()->orderBy('ordem')->get();

        $this->dispatch('coleacao-criada');

    }

    public function atualizarOrdemColecoes($items)
    {
        foreach ($items as $item) {
            Colecao::where('id', $item['value'])
                ->where('user_id', Auth::id())
                ->update(['ordem' => $item['order']]);
        }

        $this->colecoes = Auth::user()->colecoes()->orderBy('ordem')->get();
    }

    public function editarColecao($id)
    {
        $this->colecaoEditandoId = $id;
        $this->novoNomeColecao = Colecao::find($id)->nome ?? '';
    }

    public function salvarEdicaoColecao()
    {
        $this->validate([
            'novoNomeColecao' => 'required|string|min:3|max:100',
        ]);

        $colecao = Colecao::find($this->colecaoEditandoId);
        if ($colecao && $colecao->user_id === Auth::id()) {

            $colecao->update(['nome' => $this->novoNomeColecao]);

            if ($this->colecaoSelecionadaId == $this->colecaoEditandoId) {
                $this->tituloPagina = $this->novoNomeColecao;
            }
        }

        $this->reset(['colecaoEditandoId', 'novoNomeColecao']);
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

    public function selecionarColecao($id, $titulo, $icone)
    {
        $this->colecaoSelecionadaId = $id;
        $this->tituloPagina = $titulo;
        $this->iconePagina = $icone;

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
