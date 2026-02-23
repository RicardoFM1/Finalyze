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
        // Forçar a coluna status a ser string para suportar 'inativo' e outros estados futuros
        // Usamos Schema::table e change() - certifique-se que doctrine/dbal está instalado
        Schema::table('lembretes', function (Blueprint $table) {
            $table->string('status', 50)->default('andamento')->change();
        });

        Schema::table('metas', function (Blueprint $table) {
            $table->string('status', 50)->default('andamento')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
