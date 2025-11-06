<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('badges', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->text('descricao');
            $table->string('imagem_url'); // Caminho para o ícone do emblema
            $table->string('tipo'); // 'livros_finalizados', 'livros_favoritados', 'comentarios', 'antiguidade'
            $table->integer('requisito'); // Ex: 5 (para 5 livros), 365 (para 365 dias)
            $table->integer('ordem')->default(0); // Para ordenar a exibição
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('badges');
    }
};
