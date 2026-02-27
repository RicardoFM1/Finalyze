<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {

        DB::statement("ALTER TABLE metas DROP CONSTRAINT IF EXISTS metas_status_check");
        DB::statement("ALTER TABLE anotacoes DROP CONSTRAINT IF EXISTS anotacoes_status_check");
    }

    public function down(): void
    {

    }
};
