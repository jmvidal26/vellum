<?php
namespace App\Services;

use App\Models\User;
use App\Models\Badge;

class BadgeService
{
    public static function verificarEmblemas(User $user, string $tipo)
    {
        $count = 0;

        switch ($tipo) {
            case 'livros_finalizados':
                $count = $user->leiturasFinalizadas()->count();
                break;
            case 'livros_favoritados':
                $count = $user->favoritos()->count();
                break;
            case 'comentarios':
                $count = $user->comentariosClube()->count();
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
