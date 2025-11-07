<?php

namespace App\Livewire;

use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Database\Eloquent\Collection;

#[Layout('components.layouts.menu')]
#[Title('Minhas Mensagens')]
class ChatHub extends Component
{
    protected Collection $conversations;

    public function mount()
    {
        $this->loadConversations();
    }

    public function loadConversations()
    {
        $myId = Auth::id();

        $latestMessagesSubquery = Message::selectRaw('MAX(id) as last_message_id')
            ->where('sender_id', $myId)
            ->orWhere('receiver_id', $myId)
            ->groupByRaw('IF(sender_id = ?, receiver_id, sender_id)', [$myId]);

        $latestMessages = Message::whereIn('id', $latestMessagesSubquery)
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'desc')
            ->get();

        $unreadCounts = Message::selectRaw('sender_id, COUNT(*) as count')
            ->where('receiver_id', $myId)
            ->whereNull('read_at')
            ->groupBy('sender_id')
            ->pluck('count', 'sender_id');

        $this->conversations = $latestMessages->map(function ($message) use ($myId, $unreadCounts) {

            $user = $message->sender_id == $myId ? $message->receiver : $message->sender;

            $user->unread_count = $unreadCounts->get($user->id, 0);

            $user->last_message_text = ($message->sender_id == $myId ? 'Você: ' : '') . $message->text;
            $user->last_message_time = $message->created_at;

            return $user;
        });
    }

    public function render()
    {
        return view('livewire.chat-hub', [
            'conversations' => $this->conversations,
        ]);
    }
}
