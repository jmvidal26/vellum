<?php

use App\Livewire\Actions\Logout;
use App\Livewire\MinhaEstante;
use Illuminate\Support\Facades\Route;
use App\Livewire\Dashboard;
use App\Livewire\Acervo;
use App\Livewire\ClubeLivro;
use App\Livewire\ChatHub;

Route::get('/', function () {
    return redirect('/login');
});
Route::post('logout', Logout::class)->name('logout');

require __DIR__.'/auth.php';

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', Dashboard::class)
        ->name('dashboard');

    Route::get('/clube-do-livro', ClubeLivro::class)
        ->name('clube-do-livro');

    Route::get('/acervo', Acervo::class)
        ->name('acervo');

    Route::get('/minha_estante', MinhaEstante::class)
        ->name('minha_estante');

    Route::get('/livros/{livroId}', \App\Livewire\LivroDetalhes::class)
        ->name('livros.mostrar');

    Route::view('profile', 'profile')
        ->name('profile');

    Route::get('/mensagens', ChatHub::class)
        ->name('chat.hub');
});

