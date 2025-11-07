<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

class ChatPrivado extends Component
{
    public $showChat = false;
    public $selectedUser;
    public $newMessage = '';
    protected ?Collection $chatMessages = null;

    protected $listeners = ['abrirChat'];

    protected function rules()
    {
        return [
            'newMessage' => 'required|string|max:1000',
        ];
    }

    protected $messages = [
        'newMessage.required' => 'A mensagem não pode estar em branco.',
    ];

    public function mount()
    {
        $this->chatMessages = collect();
    }

    public function sendMessage()
    {
        $this->validate();

        Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $this->selectedUser->id,
            'text' => $this->newMessage,
        ]);

        $this->reset('newMessage');

        $this->loadMessages();

        $this->dispatch('limpar-chat-input');
    }

    public function abrirChat($userId)
    {
        $this->selectedUser = User::find($userId);
        if (!$this->selectedUser || $this->selectedUser->id === Auth::id()) {
            $this->closeChat();
            return;
        }

        Message::where('sender_id', $this->selectedUser->id)
            ->where('receiver_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $this->loadMessages();

        $this->showChat = true;
        $this->newMessage = '';

        $this->dispatch('messagesRead');
    }

    public function loadMessages()
    {
        if (!$this->selectedUser) {
            return;
        }

        $this->chatMessages = Message::where(function ($query) {
            $query->where('sender_id', Auth::id())
                ->where('receiver_id', $this->selectedUser->id);
        })->orWhere(function ($query) {
            $query->where('sender_id', $this->selectedUser->id)
                ->where('receiver_id', Auth::id());
        })
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function closeChat()
    {
        $this->showChat = false;
        $this->selectedUser = null;
        $this->chatMessages = collect();
        $this->newMessage = '';
    }

    public function render()
    {
        return view('livewire.chat-privado', [
            'messages' => $this->chatMessages,
        ]);
    }
}
