<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Renomear o slug do recurso de 'lembretes-avisos' para 'lembretes'
        DB::table('recursos')
            ->where('slug', 'lembretes-avisos')
            ->update(['slug' => 'lembretes', 'nome' => 'Lembretes']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('recursos')
            ->where('slug', 'lembretes')
            ->update(['slug' => 'lembretes-avisos', 'nome' => 'Lembretes e Avisos']);
    }
};
