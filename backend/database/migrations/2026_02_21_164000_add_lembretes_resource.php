<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    
    public function up(): void
    {
        DB::table('recursos')->insertOrIgnore([
            [
                'nome' => 'Lembretes e Avisos',
                'slug' => 'lembretes-avisos',
                'descricao' => 'Crie lembretes e avisos pessoais para não esquecer de nada.',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        $recursoId = DB::table('recursos')->where('slug', 'lembretes-avisos')->value('id');
        if ($recursoId) {
            $planosIds = DB::table('planos')->pluck('id');
            foreach ($planosIds as $planoId) {
                DB::table('plano_recurso')->insertOrIgnore([
                    'plano_id' => $planoId,
                    'recurso_id' => $recursoId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        $recursoId = DB::table('recursos')->where('slug', 'lembretes-avisos')->value('id');
        if ($recursoId) {
            DB::table('plano_recurso')->where('recurso_id', $recursoId)->delete();
            DB::table('recursos')->where('id', $recursoId)->delete();
        }
    }
};
