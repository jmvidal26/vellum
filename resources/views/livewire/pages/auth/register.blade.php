<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered($user = User::create($validated)));

        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <div class="text-center">
        <img class="mx-auto h-20 w-auto" src="{{ asset('imagens/logo_icon.png') }}" alt="Vellum Logo">
        <h2 class="mt-6 text-3xl font-bold text-biblioteca-800">
            Crie sua conta
        </h2>
        <p class="mt-2 text-sm text-gray-600">
            Junte-se à Biblioteca Vellum e comece a explorar.
        </p>
    </div>

    <form wire:submit="register" class="mt-8 space-y-6">
        <div>
            <x-input-label for="name" :value="__('Nome')" class="text-sm font-bold text-gray-700 tracking-wide" />
            <x-text-input wire:model="name" id="name" class="block mt-1 w-full" type="text" name="name" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" class="text-sm font-bold text-gray-700 tracking-wide" />
            <x-text-input wire:model="email" id="email" class="block mt-1 w-full" type="email" name="email" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Senha')" class="text-sm font-bold text-gray-700 tracking-wide" />
            <x-text-input wire:model="password" id="password" class="block mt-1 w-full"
                          type="password"
                          name="password"
                          required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirme sua Senha')" class="text-sm font-bold text-gray-700 tracking-wide" />
            <x-text-input wire:model="password_confirmation" id="password_confirmation" class="block mt-1 w-full"
                          type="password"
                          name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="mt-6">

            <button type="submit"
                    wire:loading.attr="disabled"
                    wire:target="register"
                    class="w-full flex justify-center bg-biblioteca-700 text-white p-3 rounded-lg font-semibold tracking-wide
                           focus:outline-none focus:shadow-outline hover:bg-biblioteca-800 shadow-lg
                           cursor-pointer transition ease-in duration-300
                           disabled:opacity-75 disabled:bg-biblioteca-600">

                <span wire:loading.remove wire:target="register" class="flex items-center whitespace-nowrap">
                    <span class="w-5 mr-3"></span>
                    <span>{{ __('Cadastrar') }}</span>
                    <span class="w-5 ml-3"></span>
                </span>

                <span wire:loading wire:target="register" class="flex items-center whitespace-nowrap">
                    <svg class="animate-spin h-5 w-5 mr-3 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </span>
            </button>

        </div>

        <p class="flex flex-col items-center justify-center mt-6 text-center text-md text-gray-500">
            <span>{{ __('Já possui uma conta?') }}</span>
            <a href="{{ route('login') }}" class="text-biblioteca-700 hover:text-biblioteca-800 no-underline hover:underline cursor-pointer transition ease-in duration-300" wire:navigate>
                {{ __('Acesse sua conta') }}
            </a>
        </p>
    </form>
</div>
