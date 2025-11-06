<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Esta linha EXCLUI a tabela
        Schema::dropIfExists('user_livro_status');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // (Opcional) Cole aqui o código da migração original
        // que criou a tabela, caso você precise reverter.
        // Ex:
        // Schema::create('user_livro_status', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        //     $table->foreignId('livro_id')->constrained()->cascadeOnDelete();
        //     $table->string('status');
        //     $table->timestamps();
        // });
    }
};
