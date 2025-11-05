<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsuarioLeitura extends Model
{
    protected $table = 'usuario_leituras';

    protected $fillable = [
      'user_id',
      'livro_id',
      'progresso_leitura',
      'status'
    ];

}
