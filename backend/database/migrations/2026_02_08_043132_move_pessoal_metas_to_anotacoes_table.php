<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $metas = DB::table('metas')->where('tipo', 'pessoal')->get();

        foreach ($metas as $meta) {
            DB::table('anotacoes')->insert([
                'usuario_id' => $meta->usuario_id,
                'titulo' => $meta->titulo,
                'descricao' => $meta->descricao,
                'icone' => $meta->icone,
                'cor' => $meta->cor,
                'prazo' => $meta->prazo,
                'status' => $meta->status === 'concluido' ? 'concluido' : 'andamento',
                'created_at' => $meta->created_at,
                'updated_at' => $meta->updated_at,
            ]);
        }

        DB::table('metas')->where('tipo', 'pessoal')->delete();
    }

    public function down(): void
    {
        
    }
};
