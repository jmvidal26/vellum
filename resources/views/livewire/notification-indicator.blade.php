<div wire:poll.2s="loadUnreadCount">
    @if($unreadCount > 0)
        <span class="absolute top-0 right-0 block h-3 w-3 -mt-1 -mr-1">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
        </span>
    @endif
</div>
