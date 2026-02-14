<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Para PostgreSQL, precisamos alterar o tipo enum ou usar string temporariamente se for o caso.
        // Como o DB_CONNECTION no .env é pgsql, usaremos uma estratégia compatível com Postgres.

        DB::statement("ALTER TABLE metas ALTER COLUMN status TYPE VARCHAR(255)");
        DB::statement("ALTER TABLE anotacoes ALTER COLUMN status TYPE VARCHAR(255)");
    }

    public function down(): void
    {
        // No down, voltamos para enum (note que isso pode falhar se houver dados 'inativo')
        // Por simplicidade em desenvolvimento, manteremos como varchar ou tentaremos restaurar o enum
    }
};
