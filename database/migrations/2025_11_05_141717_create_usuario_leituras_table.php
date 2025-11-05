<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuario_leituras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('livro_id')->constrained('livros')->onDelete('cascade');
            $table->integer('progresso_leitura')->default(0);
            $table->string('status')->default('lendo');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('usuario_leituras');
    }
};
