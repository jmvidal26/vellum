<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clube_membros', function (Blueprint $table) {
            $table->unsignedBigInteger('clube_sessao_id')->nullable();

            $table->foreign('clube_sessao_id')
                ->references('id')
                ->on('clube_sessoes')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('clube_membros', function (Blueprint $table) {
            $table->dropForeign(['clube_sessao_id']);
            $table->dropColumn('clube_sessao_id');
        });
    }
};
