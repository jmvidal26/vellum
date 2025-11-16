<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Colecao extends Model
{
    use HasFactory;

    protected $table = 'colecoes';

    /**
     * Os atributos que podem ser atribuídos em massa.
     *
     * @var array
     */
    protected $fillable = [
        'nome',
        'user_id',
        'icone',
        'icone_cor',
        'ordem'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function livros()
    {
        return $this->belongsToMany(Livro::class, 'colecao_livro');
    }
}
