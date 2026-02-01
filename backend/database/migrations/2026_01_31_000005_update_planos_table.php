<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('planos', function (Blueprint $table) {
            // Check if columns exist before dropping to avoid errors during rollback/retry
            if (Schema::hasColumn('planos', 'preco')) {
                $table->dropColumn('preco');
            }
            if (Schema::hasColumn('planos', 'valor_centavos')) {
                $table->dropColumn('valor_centavos');
            }
            if (Schema::hasColumn('planos', 'intervalo')) {
                $table->dropColumn('intervalo');
            }
            if (Schema::hasColumn('planos', 'recursos')) {
                $table->dropColumn('recursos');
            }
        });
    }

    public function down(): void
    {
        Schema::table('planos', function (Blueprint $table) {
            $table->integer('valor_centavos')->nullable();
            $table->string('intervalo')->nullable();
            $table->json('recursos')->nullable();
        });
    }
};
