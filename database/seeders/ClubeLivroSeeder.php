<?php

namespace Database\Seeders;

use App\Models\ClubeSessao;
use App\Models\Livro;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClubeLivroSeeder extends Seeder
{

    public function run(): void
    {
        $livros = Livro::all();

        foreach ($livros as $livro) {
            ClubeSessao::create([
                'livro_id' => $livro->id,
                'status' => 'ativo',
            ]);
        }
    }
}
