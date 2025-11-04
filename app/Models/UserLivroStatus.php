<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLivroStatus extends Model
{
    use HasFactory;

    protected $table = 'user_livro_status';

    protected $fillable = [
        'user_id',
        'livro_id',
        'status',
    ];
}

