<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LivroAvaliacao extends Model
{
    use HasFactory;

    protected $table = 'livro_avaliacoes';

    protected $fillable = [
        'livro_id',
        'user_id',
        'rating',
    ];

    public function livro()
    {
        return $this->belongsTo(Livro::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

