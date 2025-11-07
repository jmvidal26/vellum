<div>

    @if ($showChat && $selectedUser)
        <div
            wire:poll.5s="loadMessages"
            x-data="{ show: true }"
            x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex"
        >
            <div class="fixed inset-0 bg-gray-900 bg-opacity-50" wire:click="closeChat"></div>

            <div
                x-data
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="translate-x-full"
                class="relative ml-auto h-full w-full max-w-md bg-white shadow-xl flex flex-col"
            >

                <header class="flex items-center justify-between p-4 border-b border-biblioteca-200">
                    <div class="flex items-center gap-3">
                        @if ($selectedUser->profile_photo_path)
                            <img src="{{ asset('storage/' . $selectedUser->profile_photo_path) }}" alt="{{ $selectedUser->name }}" class="w-10 h-10 rounded-full object-cover">
                        @else
                            <div class="w-10 h-10 rounded-full flex items-center justify-center font-semibold bg-biblioteca-200 text-biblioteca-600">
                                {{ \App\Services\CommumFunctions::getIniciais($selectedUser->name) }}
                            </div>
                        @endif
                        <h3 class="text-lg font-bold text-biblioteca-800">{{ $selectedUser->name }}</h3>
                    </div>
                    <button wire:click="closeChat" class="text-biblioteca-500 hover:text-biblioteca-800 text-2xl">
                        &times;
                    </button>
                </header>

                <main class="flex-1 overflow-y-auto p-4 space-y-4 bg-biblioteca-50">
                    @forelse ($messages as $message)
                        @php $isSender = $message->sender_id === auth()->id(); @endphp
                        <div class="flex {{ $isSender ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-xs lg:max-w-sm px-4 py-2 rounded-lg {{ $isSender ? 'bg-biblioteca-700 text-white' : 'bg-white border border-biblioteca-200 text-biblioteca-700' }}">
                                <p>{{ $message->text }}</p>
                                <span
                                    class="text-xs opacity-75 mt-1 block"
                                    x-data="{ timestamp: '{{ $message->created_at->toIso8601String() }}' }"
                                    x-text="formatarHoraLocal(timestamp)"
                                >
                </span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-biblioteca-500 pt-10">
                            <i class="bi bi-chat-dots text-4xl"></i>
                            <p class="mt-2">Inicie a conversa com {{ $selectedUser->name }}.</p>
                        </div>
                    @endforelse
                </main>

                <footer class="p-4 border-t border-biblioteca-200 bg-white">

                    <form
                        wire:submit.prevent="sendMessage"
                        class="flex items-center gap-3"
                        x-data
                        @limpar-chat-input.window="$refs.chatInput.value = ''"
                    >
                        <div class="flex-1">
                        <textarea
                            x-ref="chatInput"
                            wire:model.defer="newMessage"
                            rows="1"
                            placeholder="Digite sua mensagem..."
                            class="flex-1 w-full p-2 rounded-lg border border-biblioteca-300 focus:outline-none focus:ring-2 focus:ring-biblioteca-500 resize-none"
                            @keydown.enter.prevent="$event.target.form.dispatchEvent(new Event('submit', {bubbles: true}))"
                        ></textarea>

                            @error('newMessage') <span class="text-sm text-red-600">{{ $message }}</span> @enderror

                        </div>
                        <button type="submit" class="inline-flex items-center justify-center h-10 w-10 rounded-full bg-biblioteca-700 text-white hover:bg-biblioteca-800 transition-colors duration-300">
                            <i class="bi bi-send-fill"></i>
                        </button>
                    </form>
                </footer>
            </div>
        </div>
    @endif

</div>
