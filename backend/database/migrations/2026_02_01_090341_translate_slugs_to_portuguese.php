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
        // Atualizar slugs de períodos
        DB::table('periodos')->where('slug', 'weekly')->update(['slug' => 'semanal']);
        DB::table('periodos')->where('slug', 'monthly')->update(['slug' => 'mensal']);
        DB::table('periodos')->where('slug', 'quarterly')->update(['slug' => 'trimestral']);
        DB::table('periodos')->where('slug', 'yearly')->update(['slug' => 'anual']);

        // Atualizar slugs de recursos
        DB::table('recursos')->where('slug', 'dashboard')->update(['slug' => 'painel']);
        DB::table('recursos')->where('slug', 'reports')->update(['slug' => 'relatorios']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverter para inglês
        DB::table('periodos')->where('slug', 'semanal')->update(['slug' => 'weekly']);
        DB::table('periodos')->where('slug', 'mensal')->update(['slug' => 'monthly']);
        DB::table('periodos')->where('slug', 'trimestral')->update(['slug' => 'quarterly']);
        DB::table('periodos')->where('slug', 'anual')->update(['slug' => 'yearly']);

        DB::table('recursos')->where('slug', 'painel')->update(['slug' => 'dashboard']);
        DB::table('recursos')->where('slug', 'relatorios')->update(['slug' => 'reports']);
    }
};
