<?php
namespace App\Services;

use App\Models\User;
use App\Models\Badge;
use App\Models\LivroFavorito;
use App\Models\UsuarioLeitura;
use App\Models\ClubeComentario;

class BadgeService
{

    public static function verificarEmblemas(User $user, string $tipo)
    {
        $count = 0;

        switch ($tipo) {
            case 'livros_finalizados':
                $count = UsuarioLeitura::where('user_id', $user->id)->where('status', 'finalizado')->count();
                break;
            case 'livros_favoritados':
                $count = LivroFavorito::where('user_id', $user->id)->count();
                break;
            case 'comentarios':
                $count = ClubeComentario::where('user_id', $user->id)->count();
                break;
            case 'antiguidade':
                $count = $user->created_at->diffInDays(now());
                break;
        }

        if ($count == 0) return;

        $idsEmblemasAtuais = $user->badges()->where('tipo', $tipo)->pluck('badges.id')->toArray();

        $idsNovosEmblemas = Badge::where('tipo', $tipo)
            ->where('requisito', '<=', $count)
            ->whereNotIn('id', $idsEmblemasAtuais)
            ->pluck('id');

        if ($idsNovosEmblemas->isNotEmpty()) {
            $user->badges()->attach($idsNovosEmblemas);
        }
    }
}
