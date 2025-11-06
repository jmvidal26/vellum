<?php
namespace Database\Seeders;
use App\Models\Badge;
use Illuminate\Database\Seeder;

class BadgeSeeder extends Seeder
{
    public function run(): void
    {
        Badge::truncate();

        $badges = [
            // --- Livros Finalizados ---
            ['nome' => 'Leitor Iniciante', 'descricao' => 'Finalizou seu 1º livro.', 'imagem_url' => 'images/badges/finalizado_1.png', 'tipo' => 'livros_finalizados', 'requisito' => 1],
            ['nome' => 'Maratonista Literário', 'descricao' => 'Finalizou 5 livros.', 'imagem_url' => 'images/badges/finalizado_5.png', 'tipo' => 'livros_finalizados', 'requisito' => 5], // NOVO
            ['nome' => 'Leitor Ávido', 'descricao' => 'Finalizou 10 livros.', 'imagem_url' => 'images/badges/finalizado_10.png', 'tipo' => 'livros_finalizados', 'requisito' => 10],
            ['nome' => 'Bibliotecário Honorário', 'descricao' => 'Finalizou 25 livros.', 'imagem_url' => 'images/badges/finalizado_25.png', 'tipo' => 'livros_finalizados', 'requisito' => 25], // NOVO
            ['nome' => 'Devorador de Livros', 'descricao' => 'Finalizou 50 livros.', 'imagem_url' => 'images/badges/finalizado_50.png', 'tipo' => 'livros_finalizados', 'requisito' => 50],
            ['nome' => 'Lenda Viva da Leitura', 'descricao' => 'Finalizou 100 livros. Uma verdadeira enciclopédia!', 'imagem_url' => 'images/badges/finalizado_100.png', 'tipo' => 'livros_finalizados', 'requisito' => 100], // NOVO

            // --- Livros Favoritados ---
            ['nome' => 'Curador', 'descricao' => 'Favoritou 5 livros.', 'imagem_url' => 'images/badges/favorito_5.png', 'tipo' => 'livros_favoritados', 'requisito' => 5],
            ['nome' => 'Colecionador de Pérolas', 'descricao' => 'Favoritou 10 livros. Você tem bom gosto!', 'imagem_url' => 'images/badges/favorito_10.png', 'tipo' => 'livros_favoritados', 'requisito' => 10], // NOVO
            ['nome' => 'Colecionador', 'descricao' => 'Favoritou 25 livros.', 'imagem_url' => 'images/badges/favorito_25.png', 'tipo' => 'livros_favoritados', 'requisito' => 25],
            ['nome' => 'Guardião do Tesouro Literário', 'descricao' => 'Favoritou 50 livros. Sua coleção é um tesouro!', 'imagem_url' => 'images/badges/favorito_50.png', 'tipo' => 'livros_favoritados', 'requisito' => 50], // NOVO

            // --- Comentários no Clube ---
            ['nome' => 'Debatedor', 'descricao' => 'Fez seu 1º comentário no Clube.', 'imagem_url' => 'images/badges/comentario_1.png', 'tipo' => 'comentarios', 'requisito' => 1],
            ['nome' => 'Voz Ativa', 'descricao' => 'Fez 5 comentários no Clube do Livro.', 'imagem_url' => 'images/badges/comentario_5.png', 'tipo' => 'comentarios', 'requisito' => 5], // NOVO
            ['nome' => 'Pilar da Comunidade', 'descricao' => 'Fez 20 comentários.', 'imagem_url' => 'images/badges/comentario_20.png', 'tipo' => 'comentarios', 'requisito' => 20],
            ['nome' => 'Cérebro do Clube', 'descricao' => 'Fez 50 comentários. Suas ideias enriquecem o debate!', 'imagem_url' => 'images/badges/comentario_50.png', 'tipo' => 'comentarios', 'requisito' => 50], // NOVO

            // --- Antiguidade ---
            ['nome' => 'Veterano', 'descricao' => '1 ano de conta.', 'imagem_url' => 'images/badges/antigo_1.png', 'tipo' => 'antiguidade', 'requisito' => 365],
            ['nome' => 'Guardião do Tempo', 'descricao' => '2 anos de conta na plataforma.', 'imagem_url' => 'images/badges/antigo_2.png', 'tipo' => 'antiguidade', 'requisito' => 730], // NOVO
            ['nome' => 'Membro Fundador', 'descricao' => '3 anos de conta.', 'imagem_url' => 'images/badges/antigo_3.png', 'tipo' => 'antiguidade', 'requisito' => 1095],
        ];

        foreach ($badges as $badge) {
            Badge::updateOrCreate(['nome' => $badge['nome']], $badge);
        }
    }
}
