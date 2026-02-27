<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    
    public function up(): void
    {
        
        DB::table('periodos')->where('slug', 'semanal')->update(['slug' => 'semanal']);
        DB::table('periodos')->where('slug', 'mensal')->update(['slug' => 'mensal']);
        DB::table('periodos')->where('slug', 'trimestral')->update(['slug' => 'trimestral']);
        DB::table('periodos')->where('slug', 'anual')->update(['slug' => 'anual']);

        DB::table('recursos')->where('slug', 'painel')->update(['slug' => 'painel']);
        DB::table('recursos')->where('slug', 'relatorios')->update(['slug' => 'relatorios']);
    }

    public function down(): void
    {
        
        DB::table('periodos')->where('slug', 'semanal')->update(['slug' => 'weekly']);
        DB::table('periodos')->where('slug', 'mensal')->update(['slug' => 'monthly']);
        DB::table('periodos')->where('slug', 'trimestral')->update(['slug' => 'quarterly']);
        DB::table('periodos')->where('slug', 'anual')->update(['slug' => 'yearly']);

        DB::table('recursos')->where('slug', 'painel')->update(['slug' => 'dashboard']);
        DB::table('recursos')->where('slug', 'relatorios')->update(['slug' => 'reports']);
    }
};
