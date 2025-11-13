<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('colecao_livro', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();

            $table->foreignId('colecao_id')->constrained('colecoes')->cascadeOnDelete();

            $table->foreignId('livro_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['colecao_id', 'livro_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('colecao_livro');
    }
};
