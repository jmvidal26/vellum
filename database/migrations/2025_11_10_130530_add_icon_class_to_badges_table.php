<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// O nome do arquivo não importa mais, mas a classe é esta
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('badges', function (Blueprint $table) {
            // 1. Adiciona a nova coluna
            $table->string('icon_class')->nullable()->after('descricao');

            // 2. Remove a coluna antiga (se ela existir)
            if (Schema::hasColumn('badges', 'imagem_url')) {
                $table->dropColumn('imagem_url');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('badges', function (Blueprint $table) {
            // 1. Adiciona a coluna antiga de volta
            $table->string('imagem_url')->nullable()->after('descricao');

            // 2. Remove a nova coluna
            if (Schema::hasColumn('badges', 'icon_class')) {
                $table->dropColumn('icon_class');
            }
        });
    }
};
