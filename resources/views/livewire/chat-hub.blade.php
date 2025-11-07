<div class="mx-auto">
    <div class="mb-10">
        <h2 class="text-4xl font-bold text-biblioteca-800 mb-2">Minhas Mensagens</h2>
        <p class="text-lg text-biblioteca-600">Sua caixa de entrada de conversas privadas.</p>
    </div>

    <div class="bg-white rounded-xl shadow-md border border-biblioteca-200 overflow-hidden">
        <div class="flex flex-col">
            @forelse($conversations as $user)
                <button
                    wire:click.prevent="$dispatch('abrirChat', { userId: {{ $user->id }} })"
                    class="flex items-center gap-4 p-4 w-full text-left transition-colors hover:bg-biblioteca-50 border-b border-biblioteca-100 last:border-b-0"
                >
                    <div class="flex-shrink-0">
                        @if ($user->profile_photo_path)
                            <img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="{{ $user->name }}" class="w-12 h-12 rounded-full object-cover">
                        @else
                            <div class="w-12 h-12 rounded-full flex items-center justify-center font-semibold bg-biblioteca-200 text-biblioteca-600">
                                {{ \App\Services\CommumFunctions::getIniciais($user->name) }}
                            </div>
                        @endif
                    </div>

                    <div class="flex-1 overflow-hidden">
                        <div class="flex justify-between items-center">
                            <span class="font-bold text-biblioteca-800">{{ $user->name }}</span>
                            <span class="text-xs text-biblioteca-500">{{ $user->last_message_time->diffForHumans() }}</span>
                        </div>
                        <div class="flex justify-between items-start mt-1">
                            <p class="text-sm text-biblioteca-600 truncate">
                                {{ $user->last_message_text }}
                            </p>
                            @if($user->unread_count > 0)
                                <span class="ml-2 flex-shrink-0 bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">
                                    {{ $user->unread_count }}
                                </span>
                            @endif
                        </div>
                    </div>
                </button>
            @empty
                <div class="text-center p-12">
                    <i class="bi bi-chat-square-dots text-5xl text-biblioteca-400"></i>
                    <p class="mt-4 text-biblioteca-600">Você ainda não tem nenhuma conversa.</p>
                    <p class="text-sm text-biblioteca-500">Inicie um chat com um membro no Clube do Livro.</p>
                </div>
            @endforelse
        </div>
    </div>
    <livewire:chat-privado />
</div>
