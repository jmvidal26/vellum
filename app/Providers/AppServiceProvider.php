<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\LivroAvaliacao;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        LivroAvaliacao::saved(function ($avaliacao) {
            $avaliacao->livro->updateRating();
        });

        LivroAvaliacao::deleted(function ($avaliacao) {
            $avaliacao->livro->updateRating();
        });
    }
}

