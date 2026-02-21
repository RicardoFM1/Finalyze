<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('metas', function (Blueprint $table) {
            $table->string('status', 30)->default('andamento')->change();
        });

        Schema::table('anotacoes', function (Blueprint $table) {
            $table->string('status', 30)->default('andamento')->change();
        });
    }

    public function down(): void
    {
        // Voltar para enum se necessário, mas string é mais flexível
    }
};
