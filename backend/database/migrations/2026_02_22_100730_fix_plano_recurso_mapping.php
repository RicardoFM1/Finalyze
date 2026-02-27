<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    
    public function up(): void
    {

        DB::table('plano_recurso')->truncate();

        $recursos = DB::table('recursos')->get()->keyBy('slug');

        $planos = DB::table('planos')->get()->keyBy('nome');

        if (isset($planos['Essencial'])) {
            $this->attachFeatures($planos['Essencial']->id, ['painel', 'lancamentos'], $recursos);
        }

        if (isset($planos['Pro'])) {
            $this->attachFeatures($planos['Pro']->id, ['painel', 'lancamentos', 'relatorios', 'metas', 'lembretes'], $recursos);
        }

        if (isset($planos['Premium'])) {
            $this->attachFeatures($planos['Premium']->id, ['painel', 'lancamentos', 'relatorios', 'metas', 'lembretes', 'finn-ai'], $recursos);
        }

    }

    private function attachFeatures($planoId, $slugs, $recursos)
    {
        foreach ($slugs as $slug) {
            if (isset($recursos[$slug])) {
                DB::table('plano_recurso')->insertOrIgnore([
                    'plano_id' => $planoId,
                    'recurso_id' => $recursos[$slug]->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        
    }
};
