<?php

namespace App\Servicos\Painel;

use Illuminate\Support\Facades\Auth;

class GerarResumoPainel
{
    public function executar(array $filtros = [])
    {
        $usuario = Auth::user();
        $cacheKey = "user_summary_{$usuario->id}";

        $queryBase = $usuario->lancamentos();

        if (!empty($filtros['data_inicio']) && !empty($filtros['data_fim'])) {
            $queryBase->whereBetween('data', [$filtros['data_inicio'], $filtros['data_fim']]);
        } elseif (!empty($filtros['data'])) {
            $queryBase->whereDate('data', $filtros['data']);
        }

        $calc = function () use ($queryBase) {
            $receita = (clone $queryBase)->where('tipo', 'receita')->sum('valor');
            $despesa = (clone $queryBase)->where('tipo', 'despesa')->sum('valor');
            $saldo = $receita - $despesa;

            $recentes = (clone $queryBase)->latest()->take(5)->get();

            return [
                'receita' => (float) $receita,
                'despesa' => (float) $despesa,
                'saldo' => (float) $saldo,
                'atividades_recentes' => $recentes
            ];
        };

        if (!empty($filtros)) {
            return $calc();
        }

        return cache()->remember($cacheKey, now()->addMinutes(10), $calc);
    }
}
