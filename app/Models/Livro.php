<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Livro extends Model
{
    protected $fillable = [
        'titulo',
        'resumo',
        'numero_downloads',
        'parsed_content'
    ];

    /**
     * Os atributos que devem ser convertidos (cast) para tipos nativos.
     *
     * @var array
     */
    protected $casts = [
        'parsed_content' => 'array', // Converte a coluna JSON para array (e vice-versa)
    ];

    public function autores() {
        return $this->belongsToMany(Autor::class);
    }

    public function assuntos() {
        return $this->belongsToMany(Assunto::class);
    }

    public function estantes() {
        return $this->belongsToMany(Estante::class);
    }

    public function idiomas() {
        return $this->belongsToMany(Idioma::class);
    }

    public function formatos() {
        return $this->hasMany(Formato::class);
    }
    public function livrosFavoritos() {
        return $this->hasMany(LivroFavorito::class);
    }

    public function clubeSessoes()
    {
        return $this->hasMany(ClubeSessao::class);
    }

    public function avaliacoes()
    {
        return $this->hasMany(LivroAvaliacao::class);
    }

    public function updateRating()
    {
        $this->rating = round($this->avaliacoes()->avg('rating'), 1) ?? 0.0;
        $this->save();
    }
}
