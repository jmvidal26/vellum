<?php

namespace App\Livewire;

use App\Models\ClubeComentario;
use App\Models\ClubeMembro;
use App\Models\ClubeSessao;
use App\Models\Livro;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.menu')]
#[Title('Clube do Livro')]
class ClubeLivro extends Component
{
    public $sessaoAtiva;
    public $comentarios;
    public $membros;

    public $quantidade = 10;
    public $sessoesAnteriores;
    public $novoComentario = '';

    public $livros;

    protected $listeners = ['carregarMais'];


    public function mount()
    {
        $this->livros = Livro::all();

        $this->sessaoAtiva = ClubeSessao::with('livro.autores', 'livro.formatos')
            ->where('status', 'ativo')
            ->first();

        $this->sessoesAnteriores = ClubeSessao::with('livro.autores', 'livro.formatos')
            ->where('status', 'lido')
            ->latest('data_discussao')
            ->limit(2)
            ->get();

        $this->recarregarMembros();

        $this->carregarComentarios();
    }

    public function entrarClube()
    {
        if (!auth()->user()->is_membro_clube) {
            ClubeMembro::create([
                'user_id' => auth()->id(),
                'clube_sessao_id' => $this->sessaoAtiva->id,
            ]);
            $this->recarregarMembros();

            Auth::setUser(User::find(auth()->id()));
            unset(auth()->user()->is_membro_clube);
        }
    }


    public function sairClube()
    {
        if (auth()->user()->is_membro_clube) {
            auth()->user()->inscricaoClube->delete();
            $this->recarregarMembros();

            Auth::setUser(User::find(auth()->id()));
            unset(auth()->user()->is_membro_clube);
        }
    }

    public function recarregarMembros()
    {
        $membroIds = ClubeMembro::pluck('user_id');
        $this->membros = User::whereIn('id', $membroIds)->get();
    }

    public function carregarComentarios()
    {
        if (!$this->sessaoAtiva) {
            $this->comentarios = collect();
            return;
        }

        $this->comentarios = $this->sessaoAtiva->comentarios()
            ->with('user')
            ->latest()
            ->take($this->quantidade)
            ->get()
            ->reverse();
    }

    #[On('carregarMais')]
    public function carregarMais()
    {
        $this->quantidade += 10;
        $this->carregarComentarios();
    }

    public function adicionarComentario()
    {
        $this->validate([
            'novoComentario' => 'required|string',
        ]);

        if (!$this->sessaoAtiva) return;

        $this->sessaoAtiva->comentarios()->create([
            'user_id' => auth()->id(),
            'texto' => $this->novoComentario,
        ]);

        $this->reset('novoComentario');
        $this->carregarComentarios();
        session()->flash('comentario_status', 'Comentário publicado!');

        $this->dispatch('limpar-textarea');
    }



    public function render()
    {
        return view('livewire.clube-do-livro');
    }
}
