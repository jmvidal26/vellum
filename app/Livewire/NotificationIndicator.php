<?php

namespace App\Livewire;

use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\On;

class NotificationIndicator extends Component
{
    public $unreadCount = 0;

    public function mount()
    {
        $this->loadUnreadCount();
    }

    #[On('messagesRead')]
    public function loadUnreadCount()
    {
        if (Auth::check()) {
            $this->unreadCount = Message::where('receiver_id', Auth::id())
                ->whereNull('read_at')
                ->count();
        }
    }

    public function render()
    {
        return view('livewire.notification-indicator');
    }
}
