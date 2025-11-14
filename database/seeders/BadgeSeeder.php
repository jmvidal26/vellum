<?php

namespace Database\Seeders;

use App\Models\Badge;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BadgeSeeder extends Seeder
{
    public function run(): void
    {
        // Usa a solução de "Reset Total" para limpar os emblemas antigos
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('user_badge')->truncate();
        Badge::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $badges = [

            ['nome' => 'Leitor Iniciante', 'descricao' => 'Finalizou seu 1º livro.', 'icon_class' => 'fas fa-medal', 'tipo' => 'livros_finalizados', 'requisito' => 1, 'ordem' => 10, 'tier' => 'bronze'],
            ['nome' => 'Maratonista Literário', 'descricao' => 'Finalizou 10 livros.', 'icon_class' => 'fas fa-shoe-prints', 'tipo' => 'livros_finalizados', 'requisito' => 10, 'ordem' => 11, 'tier' => 'silver'],
            ['nome' => 'Leitor Ávido', 'descricao' => 'Finalizou 25 livros.', 'icon_class' => 'fas fa-book-reader', 'tipo' => 'livros_finalizados', 'requisito' => 25, 'ordem' => 12, 'tier' => 'gold'],
            ['nome' => 'Devorador de Livros', 'descricao' => 'Finalizou 50 livros.', 'icon_class' => 'fas fa-dragon', 'tipo' => 'livros_finalizados', 'requisito' => 50, 'ordem' => 13, 'tier' => 'platinum'],
            ['nome' => 'Lenda Viva da Leitura', 'descricao' => 'Finalizou 100 livros. Uma verdadeira enciclopédia!', 'icon_class' => 'fas fa-crown', 'tipo' => 'livros_finalizados', 'requisito' => 100, 'ordem' => 14, 'tier' => 'diamond'],


            ['nome' => 'Bom Gosto', 'descricao' => 'Favoritou seu 1º livro.', 'icon_class' => 'far fa-heart', 'tipo' => 'livros_favoritados', 'requisito' => 1, 'ordem' => 20, 'tier' => 'bronze'],
            ['nome' => 'Curador', 'descricao' => 'Favoritou 5 livros.', 'icon_class' => 'fas fa-heart', 'tipo' => 'livros_favoritados', 'requisito' => 5, 'ordem' => 21, 'tier' => 'silver'],
            ['nome' => 'Colecionador', 'descricao' => 'Favoritou 10 livros.', 'icon_class' => 'far fa-gem', 'tipo' => 'livros_favoritados', 'requisito' => 10, 'ordem' => 22, 'tier' => 'gold'],
            ['nome' => 'Caçador de Pérolas', 'descricao' => 'Favoritou 25 livros.', 'icon_class' => 'fas fa-boxes', 'tipo' => 'livros_favoritados', 'requisito' => 25, 'ordem' => 23, 'tier' => 'platinum'],
            ['nome' => 'Guardião do Tesouro', 'descricao' => 'Favoritou 50 livros. Sua coleção é um tesouro!', 'icon_class' => 'fas fa-archive', 'tipo' => 'livros_favoritados', 'requisito' => 50, 'ordem' => 24, 'tier' => 'diamond'],


            ['nome' => 'Debatedor', 'descricao' => 'Fez seu 1º comentário no Clube.', 'icon_class' => 'fas fa-comment-dots', 'tipo' => 'comentarios', 'requisito' => 1, 'ordem' => 30, 'tier' => 'bronze'],
            ['nome' => 'Voz Ativa', 'descricao' => 'Fez 5 comentários no Clube do Livro.', 'icon_class' => 'fas fa-bullhorn', 'tipo' => 'comentarios', 'requisito' => 5, 'ordem' => 31, 'tier' => 'silver'],
            ['nome' => 'Pilar da Comunidade', 'descricao' => 'Fez 20 comentários.', 'icon_class' => 'fas fa-landmark', 'tipo' => 'comentarios', 'requisito' => 20, 'ordem' => 32, 'tier' => 'gold'],
            ['nome' => 'Cérebro do Clube', 'descricao' => 'Fez 50 comentários. Suas ideias enriquecem o debate!', 'icon_class' => 'far fa-lightbulb', 'tipo' => 'comentarios', 'requisito' => 50, 'ordem' => 33, 'tier' => 'diamond'],


            ['nome' => 'Novato', 'descricao' => '1 dia no Vellum.', 'icon_class' => 'fas fa-door-open', 'tipo' => 'antiguidade', 'requisito' => 1, 'ordem' => 100, 'tier' => 'bronze'],
            ['nome' => 'Residente', 'descricao' => '30 dias de conta.', 'icon_class' => 'fas fa-home', 'tipo' => 'antiguidade', 'requisito' => 30, 'ordem' => 101, 'tier' => 'silver'],
            ['nome' => 'Veterano', 'descricao' => '1 ano (365 dias) de conta.', 'icon_class' => 'fas fa-shield-alt', 'tipo' => 'antiguidade', 'requisito' => 365, 'ordem' => 102, 'tier' => 'gold'],
            ['nome' => 'Guardião do Tempo', 'descricao' => '2 anos (730 dias) de conta.', 'icon_class' => 'fas fa-hourglass-half', 'tipo' => 'antiguidade', 'requisito' => 730, 'ordem' => 103, 'tier' => 'platinum'],
            ['nome' => 'Membro Fundador', 'descricao' => '3 anos (1095 dias) de conta.', 'icon_class' => 'fas fa-anchor', 'tipo' => 'antiguidade', 'requisito' => 1095, 'ordem' => 104, 'tier' => 'diamond'],

            ['nome' => 'Sábio da Fantasia', 'descricao' => 'Completou o quiz "Clássicos da Fantasia".', 'icon_class' => 'fas fa-wand-magic-sparkles', 'tipo' => 'quiz_especifico', 'requisito' => 1, 'ordem' => 40, 'tier' => 'bronze'],
            ['nome' => 'Coração Apaixonado', 'descricao' => 'Completou o quiz "Mestres do Romance".', 'icon_class' => 'fas fa-heart', 'tipo' => 'quiz_especifico', 'requisito' => 1, 'ordem' => 41, 'tier' => 'bronze'],
            ['nome' => 'Viajante Espacial', 'descricao' => 'Completou o quiz "Futuros Distantes" (Ficção Científica).', 'icon_class' => 'fas fa-user-astronaut', 'tipo' => 'quiz_especifico', 'requisito' => 1, 'ordem' => 42, 'tier' => 'bronze'],
            ['nome' => 'Sobrevivente', 'descricao' => 'Completou o quiz "Lendas do Terror".', 'icon_class' => 'fas fa-ghost', 'tipo' => 'quiz_especifico', 'requisito' => 1, 'ordem' => 43, 'tier' => 'bronze'],
            ['nome' => 'Historiador', 'descricao' => 'Completou o quiz "Fatos Históricos".', 'icon_class' => 'fas fa-landmark', 'tipo' => 'quiz_especifico', 'requisito' => 1, 'ordem' => 44, 'tier' => 'bronze'],

            ['nome' => 'Curioso', 'descricao' => 'Completou seu 1º quiz.', 'icon_class' => 'fas fa-question-circle', 'tipo' => 'quizzes_concluidos', 'requisito' => 1, 'ordem' => 50, 'tier' => 'bronze'],
            ['nome' => 'Sabe-Tudo', 'descricao' => 'Completou 5 quizzes.', 'icon_class' => 'fas fa-brain', 'tipo' => 'quizzes_concluidos', 'requisito' => 5, 'ordem' => 51, 'tier' => 'silver'],
            ['nome' => 'Mestre dos Quizzes', 'descricao' => 'Completou 10 quizzes.', 'icon_class' => 'fas fa-graduation-cap', 'tipo' => 'quizzes_concluidos', 'requisito' => 10, 'ordem' => 52, 'tier' => 'gold'],
        ];

        foreach ($badges as $badge) {
            Badge::updateOrCreate(['nome' => $badge['nome']], $badge);
        }
    }
}
