<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {

        DB::statement("ALTER TABLE metas ALTER COLUMN status TYPE VARCHAR(255)");
        DB::statement("ALTER TABLE anotacoes ALTER COLUMN status TYPE VARCHAR(255)");
    }

    public function down(): void
    {

    }
};
