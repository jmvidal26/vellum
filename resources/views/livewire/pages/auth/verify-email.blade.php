<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    /**
     * Send an email verification notification to the user.
     */
    public function sendVerification(): void
    {
        if (Auth::user()->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);

            return;
        }

        Auth::user()->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<div>
    <div class="text-center">
        <img class="mx-auto h-20 w-auto" src="{{ asset('imagens/logo_icon.png') }}" alt="Vellum Logo">
        <h2 class="mt-6 text-3xl font-bold text-biblioteca-800">
            Verifique seu E-mail
        </h2>
    </div>

    <div class="mt-8">
        <div class="mb-4 text-sm text-biblioteca-700 text-center">
            Obrigado por se registrar! Antes de começar, você poderia verificar seu endereço de e-mail clicando no link que acabamos de enviar para você?
            <br>
            Se não o recebeu, clique no botão abaixo.
        </div>

        @if (session('status') == 'verification-link-sent')
            <div class="mb-4 font-medium text-sm text-green-600 text-center flex items-center justify-center gap-2">
                <i class="bi bi-check-circle-fill"></i>
                <span>Um novo link de verificação foi enviado.</span>
            </div>
        @endif

        <div class="mt-6 space-y-4">
            <button wire:click="sendVerification"
                    wire:loading.attr="disabled"
                    wire:target="sendVerification"
                    class="w-full flex justify-center bg-biblioteca-700 text-white p-3 rounded-lg font-semibold tracking-wide
                           focus:outline-none focus:shadow-outline hover:bg-biblioteca-800 shadow-lg
                           cursor-pointer transition ease-in duration-300
                           disabled:opacity-75 disabled:bg-biblioteca-600">

                <span wire:loading.remove wire:target="sendVerification" class="flex items-center">
                    <i class="bi bi-envelope-arrow-up-fill w-5 mr-3"></i>
                    <span>Reenviar E-mail de Verificação</span>
                </span>

                <span wire:loading wire:target="sendVerification" class="flex items-center">
                    <svg class="animate-spin h-5 w-5 mr-3 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </span>
            </button>

            <button wire:click="logout" type="submit"
                    class="w-full flex justify-center bg-transparent text-biblioteca-700 p-3 rounded-lg font-semibold tracking-wide
                           focus:outline-none focus:shadow-outline hover:bg-biblioteca-100 shadow-sm border border-biblioteca-200
                           cursor-pointer transition ease-in duration-300">
                Sair
            </button>
        </div>
    </div>
</div>
