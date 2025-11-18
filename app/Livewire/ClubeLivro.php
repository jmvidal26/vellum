<?php

namespace App\Livewire;

use App\Models\ClubeMembro;
use App\Models\ClubeSessao;
use App\Models\Livro;
use App\Models\User;
use App\Services\BadgeService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;

#[Layout('components.layouts.menu')]
#[Title('Clube do Livro')]
class ClubeLivro extends Component
{
    public $sessaoAtiva;
    public $novoComentario = '';
    public $quantidade = 10;

    protected function rules()
    {
        return [
            'novoComentario' => 'required|string|max:1000',
        ];
    }

    protected $messages = [
        'novoComentario.required' => 'O comentário não pode estar em branco.',
        'novoComentario.string' => 'O comentário deve ser um texto.',
        'novoComentario.max' => 'O comentário não pode ter mais de 1000 caracteres.',
    ];

    public function mount()
    {
        $this->sessaoAtiva = ClubeSessao::with('livro.autores', 'livro.formatos')
            ->where('status', 'ativo')
            ->first();
    }

    #[Computed]
    public function comentarios()
    {
        if (!$this->sessaoAtiva) {
            return collect();
        }

        return $this->sessaoAtiva->comentarios()
            ->with('user')
            ->latest()
            ->take($this->quantidade)
            ->get();
    }

    #[Computed]
    public function membros()
    {
        $membroIds = ClubeMembro::pluck('user_id');
        $userId = auth()->id();

        return User::whereIn('id', $membroIds)
            ->orderByRaw("id = ? DESC", [$userId])
            ->orderBy('name', 'asc')
            ->get();
    }

    #[Computed]
    public function sessoesAnteriores()
    {
        return ClubeSessao::with('livro.autores', 'livro.formatos')
            ->where('status', 'lido')
            ->latest('data_discussao')
            ->limit(2)
            ->get();
    }

    #[Computed]
    public function livros()
    {
        return Livro::all();
    }

    public function entrarClube()
    {
        if (!auth()->user()->is_membro_clube) {
            ClubeMembro::create([
                'user_id' => auth()->id(),
                'clube_sessao_id' => $this->sessaoAtiva->id,
            ]);

            unset($this->membros);

            Auth::setUser(User::find(auth()->id()));
            unset(auth()->user()->is_membro_clube);
        }
    }

    public function sairClube()
    {
        if (auth()->user()->is_membro_clube) {
            auth()->user()->inscricaoClube->delete();

            unset($this->membros);

            Auth::setUser(User::find(auth()->id()));
            unset(auth()->user()->is_membro_clube);
        }
    }

    #[On('carregarMais')]
    public function carregarMais()
    {
        $this->quantidade += 10;
    }

    public function adicionarComentario()
    {
        $this->validate();

        if (!$this->sessaoAtiva) return;

        $this->sessaoAtiva->comentarios()->create([
            'user_id' => auth()->id(),
            'texto' => $this->novoComentario,
        ]);

        $this->reset('novoComentario');

        unset($this->comentarios);

        session()->flash('comentario_status', 'Comentário publicado!');
        $this->dispatch('limpar-textarea');

        BadgeService::verificarEmblemas(auth()->user(), 'comentarios');
    }

    public function render()
    {
        return view('livewire.clube-do-livro');
    }
}
