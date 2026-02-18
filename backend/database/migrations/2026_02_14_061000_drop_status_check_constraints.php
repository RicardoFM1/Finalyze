<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // No PostgreSQL, enums criam restrições de verificação (check constraints).
        // Mesmo mudando o tipo da coluna para VARCHAR, a restrição continua lá.

        // Tentamos remover as restrições. Usamos try/catch via SQL para evitar erro se não existir.
        DB::statement("ALTER TABLE metas DROP CONSTRAINT IF EXISTS metas_status_check");
        DB::statement("ALTER TABLE anotacoes DROP CONSTRAINT IF EXISTS anotacoes_status_check");
    }

    public function down(): void
    {
        // Não é trivial restaurar o enum exato sem saber o estado anterior, 
        // mas para o fluxo do app, manter varchar é mais flexível.
    }
};
