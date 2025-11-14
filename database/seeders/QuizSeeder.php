<?php

namespace Database\Seeders;

use App\Models\Quiz;
use App\Models\Badge;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuizSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('quiz_attempts')->truncate();
        DB::table('options')->truncate();
        DB::table('questions')->truncate();
        Quiz::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->createFantasiaQuiz();
        $this->createRomanceQuiz();
        $this->createSciFiQuiz();
        $this->createTerrorQuiz();
        $this->createHistoriaQuiz();
    }

    /**
     * Quiz de Fantasia
     */
    private function createFantasiaQuiz(): void
    {
        $badge = Badge::where('nome', 'Sábio da Fantasia')->first();

        $quiz = Quiz::create([
            'titulo' => 'Clássicos da Fantasia',
            'descricao' => 'Teste seu conhecimento sobre os maiores clássicos da fantasia.',
            'ativo' => true,
            'badge_id' => $badge ? $badge->id : null
        ]);

        $q1 = $quiz->questions()->create([
            'texto_pergunta' => 'Em "O Senhor dos Anéis", qual o nome do mago cinzento?'
        ]);
        $q1->options()->createMany([
            ['texto_opcao' => 'Alvo Dumbledore', 'is_correct' => false],
            ['texto_opcao' => 'Gandalf', 'is_correct' => true],
            ['texto_opcao' => 'Merlin', 'is_correct' => false],
            ['texto_opcao' => 'Elminster', 'is_correct' => false],
        ]);

        $q2 = $quiz->questions()->create([
            'texto_pergunta' => 'Qual animal é o símbolo da casa Grifinória em "Harry Potter"?'
        ]);
        $q2->options()->createMany([
            ['texto_opcao' => 'Um Leão', 'is_correct' => true],
            ['texto_opcao' => 'Uma Serpente', 'is_correct' => false],
            ['texto_opcao' => 'Uma Águia', 'is_correct' => false],
            ['texto_opcao' => 'Um Texugo', 'is_correct' => false],
        ]);

        $q3 = $quiz->questions()->create([
            'texto_pergunta' => 'Quem é o autor de "As Crônicas de Nárnia"?'
        ]);
        $q3->options()->createMany([
            ['texto_opcao' => 'J.R.R. Tolkien', 'is_correct' => false],
            ['texto_opcao' => 'George R.R. Martin', 'is_correct' => false],
            ['texto_opcao' => 'J.K. Rowling', 'is_correct' => false],
            ['texto_opcao' => 'C.S. Lewis', 'is_correct' => true],
        ]);
    }

    /**
     * Quiz de Romance
     */
    private function createRomanceQuiz(): void
    {
        $badge = Badge::where('nome', 'Coração Apaixonado')->first();

        $quiz = Quiz::create([
            'titulo' => 'Mestres do Romance',
            'descricao' => 'Você conhece as maiores histórias de amor da literatura?',
            'ativo' => true,
            'badge_id' => $badge ? $badge->id : null
        ]);

        $q1 = $quiz->questions()->create([
            'texto_pergunta' => 'Quem escreveu "Orgulho e Preconceito"?'
        ]);
        $q1->options()->createMany([
            ['texto_opcao' => 'Jane Austen', 'is_correct' => true],
            ['texto_opcao' => 'Charlotte Brontë', 'is_correct' => false],
            ['texto_opcao' => 'Mary Shelley', 'is_correct' => false],
            ['texto_opcao' => 'Stephen King', 'is_correct' => false],
        ]);

        $q2 = $quiz->questions()->create([
            'texto_pergunta' => 'Qual destes casais NÃO é de uma obra de Shakespeare?'
        ]);
        $q2->options()->createMany([
            ['texto_opcao' => 'Romeu e Julieta', 'is_correct' => false],
            ['texto_opcao' => 'Beatriz e Benedito', 'is_correct' => false],
            ['texto_opcao' => 'Elizabeth Bennet e Mr. Darcy', 'is_correct' => true],
            ['texto_opcao' => 'Otelo e Desdêmona', 'is_correct' => false],
        ]);

        $q3 = $quiz->questions()->create([
            'texto_pergunta' => 'Qual o nome do casal principal da série de livros "Outlander"?'
        ]);
        $q3->options()->createMany([
            ['texto_opcao' => 'Bella e Edward', 'is_correct' => false],
            ['texto_opcao' => 'Claire e Jamie', 'is_correct' => true],
            ['texto_opcao' => 'Hazel e Gus', 'is_correct' => false],
            ['texto_opcao' => 'Tessa e Hardin', 'is_correct' => false],
        ]);
    }

    /**
     * Quiz de Ficção Científica
     */
    private function createSciFiQuiz(): void
    {
        $badge = Badge::where('nome', 'Viajante Espacial')->first();

        $quiz = Quiz::create([
            'titulo' => 'Futuros Distantes',
            'descricao' => 'Desafie sua mente com os conceitos da Ficção Científica.',
            'ativo' => true,
            'badge_id' => $badge ? $badge->id : null
        ]);

        $q1 = $quiz->questions()->create([
            'texto_pergunta' => 'Quem formulou as "Três Leis da Robótica"?'
        ]);
        $q1->options()->createMany([
            ['texto_opcao' => 'Arthur C. Clarke', 'is_correct' => false],
            ['texto_opcao' => 'Philip K. Dick', 'is_correct' => false],
            ['texto_opcao' => 'Isaac Asimov', 'is_correct' => true],
            ['texto_opcao' => 'Frank Herbert', 'is_correct' => false],
        ]);

        $q2 = $quiz->questions()->create([
            'texto_pergunta' => 'Qual o nome da inteligência artificial vilanesca em "2001: Uma Odisseia no Espaço"?'
        ]);
        $q2->options()->createMany([
            ['texto_opcao' => 'HAL 9000', 'is_correct' => true],
            ['texto_opcao' => 'Skynet', 'is_correct' => false],
            ['texto_opcao' => 'Matrix', 'is_correct' => false],
            ['texto_opcao' => 'GLaDOS', 'is_correct' => false],
        ]);

        $q3 = $quiz->questions()->create([
            'texto_pergunta' => 'Em "Duna", a especiaria que permite viagens espaciais é chamada de:'
        ]);
        $q3->options()->createMany([
            ['texto_opcao' => 'Unobtainium', 'is_correct' => false],
            ['texto_opcao' => 'Vibranium', 'is_correct' => false],
            ['texto_opcao' => 'Kryptonita', 'is_correct' => false],
            ['texto_opcao' => 'Melange', 'is_correct' => true],
        ]);
    }

    /**
     * Quiz de Terror
     */
    private function createTerrorQuiz(): void
    {
        $badge = Badge::where('nome', 'Sobrevivente')->first();

        $quiz = Quiz::create([
            'titulo' => 'Lendas do Terror',
            'descricao' => 'Você tem coragem de testar seu conhecimento sombrio?',
            'ativo' => true,
            'badge_id' => $badge ? $badge->id : null
        ]);

        $q1 = $quiz->questions()->create([
            'texto_pergunta' => 'Qual autor é famoso por criar o "horror cósmico" e entidades como Cthulhu?'
        ]);
        $q1->options()->createMany([
            ['texto_opcao' => 'Stephen King', 'is_correct' => false],
            ['texto_opcao' => 'H.P. Lovecraft', 'is_correct' => true],
            ['texto_opcao' => 'Edgar Allan Poe', 'is_correct' => false],
            ['texto_opcao' => 'Shirley Jackson', 'is_correct' => false],
        ]);

        $q2 = $quiz->questions()->create([
            'texto_pergunta' => 'Qual é o nome do hotel assombrado no livro "O Iluminado"?'
        ]);
        $q2->options()->createMany([
            ['texto_opcao' => 'Hotel Bates', 'is_correct' => false],
            ['texto_opcao' => 'Hotel Overlook', 'is_correct' => true],
            ['texto_opcao' => 'A Casa da Colina', 'is_correct' => false],
            ['texto_opcao' => 'Hotel Cecil', 'is_correct' => false],
        ]);

        $q3 = $quiz->questions()->create([
            'texto_pergunta' => 'Quem escreveu "Frankenstein"?'
        ]);
        $q3->options()->createMany([
            ['texto_opcao' => 'Mary Shelley', 'is_correct' => true],
            ['texto_opcao' => 'Bram Stoker', 'is_correct' => false],
            ['texto_opcao' => 'Oscar Wilde', 'is_correct' => false],
            ['texto_opcao' => 'Dr. Victor Frankenstein', 'is_correct' => false],
        ]);
    }

    /**
     * Quiz de História
     */
    private function createHistoriaQuiz(): void
    {
        $badge = Badge::where('nome', 'Historiador')->first();

        $quiz = Quiz::create([
            'titulo' => 'Fatos Históricos',
            'descricao' => 'Viaje no tempo com perguntas sobre fatos e ficção histórica.',
            'ativo' => true,
            'badge_id' => $badge ? $badge->id : null
        ]);

        $q1 = $quiz->questions()->create([
            'texto_pergunta' => 'Qual evento histórico é o cenário principal de "O Diário de Anne Frank"?'
        ]);
        $q1->options()->createMany([
            ['texto_opcao' => 'Primeira Guerra Mundial', 'is_correct' => false],
            ['texto_opcao' => 'Segunda Guerra Mundial', 'is_correct' => true],
            ['texto_opcao' => 'Revolução Francesa', 'is_correct' => false],
            ['texto_opcao' => 'Guerra Fria', 'is_correct' => false],
        ]);

        $q2 = $quiz->questions()->create([
            'texto_pergunta' => 'O livro "Sapiens: Uma Breve História da Humanidade" foi escrito por:'
        ]);
        $q2->options()->createMany([
            ['texto_opcao' => 'Jared Diamond', 'is_correct' => false],
            ['texto_opcao' => 'Maquiavel', 'is_correct' => false],
            ['texto_opcao' => 'Yuval Noah Harari', 'is_correct' => true],
            ['texto_opcao' => 'Leandro Karnal', 'is_correct' => false],
        ]);

        $q3 = $quiz->questions()->create([
            'texto_pergunta' => 'Qual livro de Ken Follett foca na construção de uma catedral na Idade Média?'
        ]);
        $q3->options()->createMany([
            ['texto_opcao' => 'O Nome da Rosa', 'is_correct' => false],
            ['texto_opcao' => 'A Guerra dos Tronos', 'is_correct' => false],
            ['texto_opcao' => 'O Código Da Vinci', 'is_correct' => false],
            ['texto_opcao' => 'Os Pilares da Terra', 'is_correct' => true],
        ]);
    }
}
