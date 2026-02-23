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
        // Slugs: painel, lancamentos, relatorios, metas, finn-ai, lembretes

        // Limpar mapeamentos existentes para garantir consistência
        DB::table('plano_recurso')->truncate();

        $recursos = DB::table('recursos')->get()->keyBy('slug');

        $planos = DB::table('planos')->get()->keyBy('nome');

        // Essencial: painel, lancamentos
        if (isset($planos['Essencial'])) {
            $this->attachFeatures($planos['Essencial']->id, ['painel', 'lancamentos'], $recursos);
        }

        // Pro: painel, lancamentos, relatorios, metas, lembretes
        if (isset($planos['Pro'])) {
            $this->attachFeatures($planos['Pro']->id, ['painel', 'lancamentos', 'relatorios', 'metas', 'lembretes'], $recursos);
        }

        // Premium: all
        if (isset($planos['Premium'])) {
            $this->attachFeatures($planos['Premium']->id, ['painel', 'lancamentos', 'relatorios', 'metas', 'lembretes', 'finn-ai'], $recursos);
        }

        // Se houver "Plano premium" (com P minúsculo ou variações), remover ou ignorar.
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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Não é necessário truncar no down, pois isso é uma correção de dados.
    }
};
