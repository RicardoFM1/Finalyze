<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    
    public function up(): void
    {
        
        DB::table('recursos')
            ->where('slug', 'lembretes-avisos')
            ->update(['slug' => 'lembretes', 'nome' => 'Lembretes']);
    }

    public function down(): void
    {
        DB::table('recursos')
            ->where('slug', 'lembretes')
            ->update(['slug' => 'lembretes-avisos', 'nome' => 'Lembretes e Avisos']);
    }
};
