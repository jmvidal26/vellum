<?php

use App\Models\User;
use App\Models\LivroFavorito;
use App\Models\UsuarioLeitura;
use App\Services\BadgeService;
use App\Models\Badge;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

new class extends Component
{
    use WithFileUploads;

    public string $name = '';
    public string $email = '';
    public $photo;
    public bool $deletingPhoto = false;

    public int $favoritosCount = 0;
    public int $andamentoCount = 0;
    public int $finalizadosCount = 0;

    public $todosEmblemas = [];
    public $meusEmblemasIds = [];

    protected $listeners = ['livroFinalizado' => 'atualizarContagens'];

    public function mount(): void
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;

        $this->atualizarContagens();

        BadgeService::verificarEmblemas($user, 'antiguidade');
        BadgeService::verificarEmblemas($user, 'livros_finalizados');
        BadgeService::verificarEmblemas($user, 'livros_favoritados');
        BadgeService::verificarEmblemas($user, 'comentarios');

        $this->todosEmblemas = Badge::orderBy('tipo')->orderBy('ordem')->orderBy('requisito')->get();

        $this->meusEmblemasIds = $user->fresh()->badges->pluck('id')->toArray();
    }

    public function atualizarContagens(): void
    {
        $userId = Auth::id();
        $this->favoritosCount = LivroFavorito::where('user_id', $userId)->count();

        $this->andamentoCount = UsuarioLeitura::where('user_id', $userId)
            ->where('status', 'lendo')
            ->count();

        $this->finalizadosCount = UsuarioLeitura::where('user_id', $userId)
            ->where('status', 'finalizado')
            ->count();
    }

    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            'photo' => ['nullable', 'image', 'max:1024'],
        ]);

        if ($this->deletingPhoto) {
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            $validated['profile_photo_path'] = null;

        } elseif ($this->photo) {
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            $validated['profile_photo_path'] = $this->photo->store('profile-photos', 'public');
        }

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->deletingPhoto = false;
        $this->dispatch('profile-updated');

        $this->redirect(route('profile'), navigate: true);
    }

    public function removePhoto(): void
    {
        $this->photo = null;
        $this->deletingPhoto = true;
    }

    public function sendVerification(): void
    {
        $user = Auth::user();
        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));
            return;
        }
        $user->sendEmailVerificationNotification();
        Session::flash('status', 'verification-link-sent');
    }

    public function getBadgeTypeTitle(string $tipo): string
    {
        return match ($tipo) {
            'livros_finalizados' => 'Conquistas de Leitura',
            'livros_favoritados' => 'Conquistas de Colecionador',
            'comentarios' => 'Conquistas de Comunidade',
            'antiguidade' => 'Conquistas de Veterano',
            'quiz_especifico', 'quizzes_concluidos' => 'Conquistas de Quiz',
            default => 'Outras Conquistas',
        };
    }
}; ?>

<section
    wire:ignore.self
    x-data="{
        isCropping: false,
        imageToCrop: null,
        cropper: null,

        handleFileSelect(event) {
            const file = event.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = (e) => {
                this.imageToCrop = e.target.result;
                this.isCropping = true;

                this.$nextTick(() => {
                    if (this.cropper) {
                        this.cropper.destroy();
                    }
                    this.cropper = new Cropper(this.$refs.croppingImage, {
                        aspectRatio: 1 / 1,
                        viewMode: 2,
                        dragMode: 'move',
                        background: false,
                        autoCropArea: 0.8,
                    });
                });
            };
            reader.readAsDataURL(file);
        },

        cropAndUpload() {
            if (!this.cropper) return;

            const wire = this.$wire;

            this.cropper.getCroppedCanvas({
                width: 256,
                height: 256,
                imageSmoothingQuality: 'high',
            }).toBlob((blob) => {
                const file = new File([blob], 'avatar.png', { type: 'image/png' });


                wire.upload('photo', file,
                    (uploadedFilename) => {
                        this.isCropping = false;
                        this.cropper.destroy();
                        this.cropper = null;
                        this.imageToCrop = null;

                        wire.set('deletingPhoto', false);
                    },
                    () => {},
                    (event) => {}
                );
            }, 'image/png');
        },

        cancelCrop() {
            this.isCropping = false;
            if (this.cropper) {
                this.cropper.destroy();
            }
            this.cropper = null;
            this.imageToCrop = null;
            this.$refs.fileInput.value = null;
        }
    }"
>

    <form wire:submit="updateProfileInformation" class="space-y-6">

        <div class="flex flex-col sm:flex-row items-center gap-6">
            <div>
                <span class="block h-20 w-20 rounded-full bg-biblioteca-100 overflow-hidden">
                    @if ($photo)
                        <img src="{{ $photo->temporaryUrl() }}" class="h-full w-full object-cover">
                    @elseif (Auth::user()->profile_photo_path && !$deletingPhoto)
                        <img src="{{ asset('storage/' . Auth::user()->profile_photo_path) }}" alt="{{ $name }}" class="h-full w-full object-cover">
                    @else
                        <svg class="h-full w-full text-biblioteca-400" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    @endif
                </span>
            </div>

            <div class="flex-grow">
                <input type="file" class="hidden"
                       id="photo-input-{{ $this->getId() }}"
                       x-ref="fileInput"
                       @change="handleFileSelect($event)"
                       accept="image/png, image/jpeg, image/webp">

                <x-secondary-button
                    x-on:click.prevent="document.getElementById('photo-input-{{ $this->getId() }}').click()"
                    class="focus:!ring-biblioteca-500">
                    {{ __('Selecionar Nova Foto') }}
                </x-secondary-button>
                <div wire:loading wire:target="photo" class="text-sm text-biblioteca-600 mt-1">
                    Carregando foto recortada...
                </div>
                <x-input-error class="mt-2" :messages="$errors->get('photo')" />
            </div>

            @if ( (Auth::user()->profile_photo_path && !$deletingPhoto) || $photo )
                <div class="ml-auto">
                    <x-secondary-button
                        wire:click.prevent="removePhoto"
                        class="!text-red-600 focus:!ring-red-500 !border-red-300 hover:!bg-red-50">
                        <div wire:loading wire:target="removePhoto" class="border-t-transparent border-solid animate-spin rounded-full border-red-500 border-2 h-4 w-4 mr-2"></div>
                        <span>{{ __('Remover Foto') }}</span>
                    </x-secondary-button>
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-6">

            <div class="bg-biblioteca-50 border border-biblioteca-200 rounded-lg p-4 flex items-center gap-4">
                <div class="flex-shrink-0 w-12 h-12 flex items-center justify-center bg-red-100 rounded-full">
                    <i class="bi bi-heart-fill text-xl text-red-600"></i>
                </div>
                <div>
                    <div class="text-2xl font-bold text-biblioteca-800">{{ $favoritosCount }}</div>
                    <div class="text-sm font-medium text-biblioteca-600">Livros Favoritos</div>
                </div>
            </div>

            <div class="bg-biblioteca-50 border border-biblioteca-200 rounded-lg p-4 flex items-center gap-4">
                <div class="flex-shrink-0 w-12 h-12 flex items-center justify-center bg-blue-100 rounded-full">
                    <i class="bi bi-book-half text-xl text-blue-600"></i>
                </div>
                <div>
                    <div class="text-2xl font-bold text-biblioteca-800">{{ $andamentoCount }}</div>
                    <div class="text-sm font-medium text-biblioteca-600">Livros em Andamento</div>
                </div>
            </div>

            <div class="bg-biblioteca-50 border border-biblioteca-200 rounded-lg p-4 flex items-center gap-4">
                <div class="flex-shrink-0 w-12 h-12 flex items-center justify-center bg-green-100 rounded-full">
                    <i class="bi bi-check2-circle text-xl text-green-600"></i>
                </div>
                <div>
                    <div class="text-2xl font-bold text-biblioteca-800">{{ $finalizadosCount }}</div>
                    <div class="text-sm font-medium text-biblioteca-600">Livros Finalizados</div>
                </div>
            </div>

        </div>

        <header class="mt-8 border-t border-biblioteca-200 pt-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <h2 class="text-2xl font-bold text-biblioteca-800">
                    {{ __('Minhas Conquistas') }}
                </h2>

                <span class="mt-1 sm:mt-0 text-lg font-medium text-biblioteca-600">
                    {{ count($meusEmblemasIds) }} / {{ count($todosEmblemas) }}
                </span>
            </div>
            <p class="mt-2 text-biblioteca-600">
                {{ __("Emblemas desbloqueados com sua atividade na plataforma.") }}
            </p>
        </header>

        <div class="flex flex-wrap justify-center gap-4">

            @php $currentType = ''; @endphp

            @foreach($todosEmblemas as $badge)
                @php
                    $hasBadge = in_array($badge->id, $meusEmblemasIds);

                    $tierClasses = '';

                    if ($hasBadge) {
                        $tierClasses = 'bg-orange-600 text-white shadow-lg shadow-orange-600/30';

                        switch ($badge->tier) {
                            case 'silver':
                                $tierClasses = 'bg-gray-400 text-gray-900 shadow-lg shadow-gray-400/30';
                                break;
                            case 'gold':
                                $tierClasses = 'bg-yellow-400 text-yellow-900 shadow-lg shadow-yellow-400/30';
                                break;

                            case 'platinum':
                                $tierClasses = 'bg-cyan-400 text-cyan-900 shadow-lg shadow-cyan-400/30';
                                break;

                            case 'diamond':
                                $tierClasses = 'bg-violet-400 text-violet-900 shadow-lg shadow-violet-400/30';
                                break;
                            case 'bronze':
                            default:
                                break;
                        }
                    } else {
                        $tierClasses = 'bg-biblioteca-100 text-biblioteca-400 opacity-60 grayscale hover:opacity-100 hover:grayscale-0';
                    }
                @endphp

                @if ($badge->tipo !== $currentType)
                    @php $currentType = $badge->tipo; @endphp
                    <h3 class="w-full text-lg font-semibold text-biblioteca-700 mt-6 mb-2 border-b border-biblioteca-200">
                        {{ $this->getBadgeTypeTitle($badge->tipo) }}
                    </h3>
                @endif

                <div x-data="{ showTooltip: false }" class="w-20 relative">

                    <div
                        @mouseenter="showTooltip = true"
                        @mouseleave="showTooltip = false"
                        class="relative transition-all duration-300 aspect-square rounded-lg flex items-center justify-center {{ $tierClasses }}"
                    >

                        <i class="{{ $badge->icon_class ?? 'fa-solid fa-question' }} text-4xl"></i>

                        @if($hasBadge)
                            <div class="absolute -top-1 -right-1 bg-green-500 text-white rounded-full h-5 w-5 flex items-center justify-center border-2 border-white shadow">
                                <i class="bi bi-check text-xs font-bold"></i>
                            </div>
                        @endif
                    </div>

                    <div x-show="showTooltip"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-1"
                         class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-max max-w-xs p-2 bg-gray-900 text-white text-xs rounded-md shadow-lg pointer-events-none"
                         x-cloak
                    >
                        <span class="font-bold block">{{ $badge->nome }}</span>
                        <span class="block">{{ $badge->descricao }}</span>
                    </div>

                </div>
            @endforeach
        </div>

        <header class="mt-6 border-t border-biblioteca-200 pt-6">
            <h2 class="text-2xl font-bold text-biblioteca-800">
                {{ __('Informações do Perfil') }}
            </h2>
            <p class="mt-2 text-biblioteca-600">
                {{ __("Atualize as informações do seu perfil e endereço de e-mail.") }}
            </p>
        </header>

        <div>
            <x-input-label for="name" :value="__('Nome')" />
            <x-text-input wire:model="name" id="name" name="name" type="text" class="mt-1 block w-full" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input wire:model="email" id="email" name="email" type="email" class="mt-1 block w-full" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
            @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Seu e-mail não foi verificado.') }}
                        <button wire:click.prevent="sendVerification" class="underline text-sm text-biblioteca-600 hover:text-biblioteca-800 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-biblioteca-500">
                            {{ __('Clique aqui para reenviar o e-mail de verificação.') }}
                        </button>
                    </p>
                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('Um novo link de verificação foi enviado para o seu e-mail.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button class="!bg-biblioteca-700 hover:!bg-biblioteca-800 focus:!bg-biblioteca-800 focus:!ring-biblioteca-500">
                {{ __('Salvar Alterações') }}
            </x-primary-button>

            <div x-data="{ shown: false, timeout: null }"
                 @profile-updated.window="shown = true; clearTimeout(timeout); timeout = setTimeout(() => { shown = false }, 2000)"
                 x-show="shown"
                 x-transition:leave.opacity.duration.1500ms
                 style="display: none;"
                 class="text-sm text-gray-600 me-3">
                {{ __('Salvo.') }}
            </div>
        </div>

    </form>

    <div x-show="isCropping"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4">

        <div x-show="isCropping"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="absolute inset-0 bg-black bg-opacity-75"></div>

        <div x-show="isCropping"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="relative bg-white rounded-lg shadow-xl w-full max-w-lg p-6">

            <h3 class="text-xl font-bold text-biblioteca-800 mb-4">Enquadrar Foto</h3>

            <div class="img-container mb-4">
                <img x-ref="croppingImage" :src="imageToCrop" alt="Imagem para recortar">
            </div>

            <div class="flex justify-end gap-4">
                <x-secondary-button @click="cancelCrop()">
                    {{ __('Cancelar') }}
                </x-secondary-button>

                <x-primary-button
                    @click="cropAndUpload()"
                    wire:loading.attr="disabled"
                    wire:target="photo"
                    class="!bg-biblioteca-700 hover:!bg-biblioteca-800 focus:!bg-biblioteca-800 focus:!ring-biblioteca-500">
                    {{ __('Definir Imagem') }}
                </x-primary-button>
            </div>
        </div>
    </div>

</section>

