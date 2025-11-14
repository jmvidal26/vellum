<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('colecoes', function (Blueprint $table) {
            $table->string('icone_cor', 7)->nullable()->default('#6B7280')->after('icone'); // Default: Tailwind Gray-500
        });
    }

    public function down(): void
    {
        Schema::table('colecoes', function (Blueprint $table) {
            $table->dropColumn('icone_cor');
        });
    }
};
