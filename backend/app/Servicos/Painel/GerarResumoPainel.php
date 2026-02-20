<?php

namespace App\Servicos\Painel;

use Illuminate\Support\Facades\Auth;

class GerarResumoPainel
{
    public function executar(array $filtros = [])
    {
        $usuario = Auth::user();
        $filtrosHash = md5(json_encode($filtros));
        $cacheKey = "user_summary_{$usuario->id}_{$filtrosHash}";

        $calc = function () use ($usuario, $filtros) {
            // Se não houver filtros de data, pegamos o mês atual para Receita/Despesa
            $queryReceitaDespesa = $usuario->lancamentos();

            if (!empty($filtros['data_inicio']) && !empty($filtros['data_fim'])) {
                $queryReceitaDespesa->whereBetween('data', [$filtros['data_inicio'], $filtros['data_fim']]);
            } elseif (!empty($filtros['data'])) {
                $data = $filtros['data'];
                if (str_contains($data, ' to ')) {
                    $parts = explode(' to ', $data);
                    $queryReceitaDespesa->whereBetween('data', [$parts[0], $parts[1] ?? $parts[0]]);
                } else {
                    $queryReceitaDespesa->whereDate('data', $data);
                }
            } else {
                // Default: Mês Atual
                $queryReceitaDespesa->whereMonth('data', now()->month)
                    ->whereYear('data', now()->year);
            }

            $receita = (clone $queryReceitaDespesa)->where('tipo', 'receita')->sum('valor');
            $despesa = (clone $queryReceitaDespesa)->where('tipo', 'despesa')->sum('valor');

            // Saldo Gerar (Sempre o total real, a menos que haja filtro específico de data?)
            // Geralmente saldo em conta é o total de tudo. 
            // Se houver filtro de data, podemos manter o saldo como o "Resultado do Período"
            // mas se não houver, deve ser o Saldo Geral.

            if (!empty($filtros['data_inicio']) || !empty($filtros['data'])) {
                $saldo = $receita - $despesa; // Resultado do período filtrado
            } else {
                $totalRec = $usuario->lancamentos()->where('tipo', 'receita')->sum('valor');
                $totalDes = $usuario->lancamentos()->where('tipo', 'despesa')->sum('valor');
                $saldo = $totalRec - $totalDes;
            }

            $recentes = $usuario->lancamentos()->latest()->take(5)->get();

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
