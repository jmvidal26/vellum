<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use App\Models\LivroAvaliacao;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Transport\Dsn;
use Symfony\Component\Mailer\Transport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('app.env') === 'production' || config('app.env') === 'local') {
            URL::forceScheme('https');
        }

        LivroAvaliacao::saved(function ($avaliacao) {
            $avaliacao->livro->updateRating();
        });

        LivroAvaliacao::deleted(function ($avaliacao) {
            $avaliacao->livro->updateRating();
        });

        Mail::extend('mailtrap', function () {
            $config = config('services.mailtrap');
            $token = $config['token'] ?? '';
            $dsnString = 'mailtrap+api://' . $token . '@default';

            $dsn = Dsn::fromString($dsnString);

            $transportFactory = new Transport(Transport::getDefaultFactories());

            return $transportFactory->fromDsnObject($dsn);
        });
    }
}
