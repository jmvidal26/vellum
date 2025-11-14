<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    use HasFactory;

    /**
     * Os atributos que podem ser atribuídos em massa.
     *
     * @var array
     */
    protected $fillable = [
        'nome',
        'descricao',
        'icon_class',
        'tipo',
        'requisito',
        'ordem',
        'tier'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_badge');
    }
}
