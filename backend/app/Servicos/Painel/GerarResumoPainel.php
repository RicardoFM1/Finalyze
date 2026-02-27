<?php

namespace App\Servicos\Painel;

use Illuminate\Support\Facades\Auth;

class GerarResumoPainel
{
    public static function limparCacheUsuario(int $userId): void
    {
        $metaKey = "user_summary_keys_{$userId}";
        $keys = cache()->get($metaKey, []);
        foreach ($keys as $key) {
            cache()->forget($key);
        }
        cache()->forget($metaKey);
    }

    public function executar(array $filtros = [])
    {
        $workspaceId = app('workspace_id');
        $usuario = \App\Models\Usuario::findOrFail($workspaceId);
        $limitRecent = isset($filtros['limit_recent']) ? (int) $filtros['limit_recent'] : 100;
        $limitRecent = max(5, min(200, $limitRecent));

        $filtrosHash = md5(json_encode($filtros));
        $cacheKey = "user_summary_{$workspaceId}_{$filtrosHash}";

        $calc = function () use ($usuario, $filtros, $limitRecent) {
            $query = $usuario->lancamentos();

            if (!empty($filtros['descricao'])) {
                $query->where('descricao', 'like', '%' . $filtros['descricao'] . '%');
            }
            if (!empty($filtros['categoria'])) {
                if (is_array($filtros['categoria'])) {
                    $query->whereIn('categoria', $filtros['categoria']);
                } else {
                    $query->where('categoria', $filtros['categoria']);
                }
            }
            if (!empty($filtros['tipo']) && $filtros['tipo'] !== 'todos') {
                $query->where('tipo', $filtros['tipo']);
            }
            if (!empty($filtros['valor'])) {
                $query->where('valor', $filtros['valor']);
            }

            if (isset($filtros['data_inicio']) && isset($filtros['data_fim'])) {
                $query->whereBetween('data', [$filtros['data_inicio'], $filtros['data_fim']]);
            } elseif (isset($filtros['data'])) {
                $data = $filtros['data'];
                if (str_contains($data, ' to ')) {
                    $parts = explode(' to ', $data);
                    $query->whereBetween('data', [$parts[0], $parts[1] ?? $parts[0]]);
                } else {
                    $query->whereDate('data', $data);
                }
            }

            $dateFilterActive = isset($filtros['data_inicio']) || isset($filtros['data_active']) || !empty($filtros['data']);

            if ($dateFilterActive) {
                $periodo_label = "filters.period_custom";
                $data_inicio = $filtros['data_inicio'] ?? null;
                $data_fim = $filtros['data_fim'] ?? null;
                if (!$data_inicio && isset($filtros['data'])) {
                    if (str_contains($filtros['data'], ' to ')) {
                        $parts = explode(' to ', $filtros['data']);
                        $data_inicio = $parts[0];
                        $data_fim = $parts[1] ?? $parts[0];
                    } else {
                        $data_inicio = $filtros['data'];
                        $data_fim = $filtros['data'];
                    }
                }
            } else {
                $periodo_label = "filters.period_all";
                $data_inicio = null;
                $data_fim = null;
            }

            $receita = (clone $query)->where('tipo', 'receita')->sum('valor');
            $despesa = (clone $query)->where('tipo', 'despesa')->sum('valor');
            $saldo = $receita - $despesa;

            $evolucaoQuery = $usuario->lancamentos();
            
            if (!empty($filtros['descricao'])) $evolucaoQuery->where('descricao', 'like', '%' . $filtros['descricao'] . '%');
            if (!empty($filtros['categoria'])) {
                if (is_array($filtros['categoria'])) $evolucaoQuery->whereIn('categoria', $filtros['categoria']);
                else $evolucaoQuery->where('categoria', $filtros['categoria']);
            }
            if (!empty($filtros['valor'])) $evolucaoQuery->where('valor', $filtros['valor']);

            if (isset($data_inicio) && isset($data_fim)) {
                $evolStart = \Carbon\Carbon::parse($data_inicio)->startOfMonth();
                $evolEnd = \Carbon\Carbon::parse($data_fim)->endOfMonth();
            } else {
                $evolStart = \Carbon\Carbon::now()->subMonths(5)->startOfMonth();
                $evolEnd = \Carbon\Carbon::now()->endOfMonth();
            }

            $evolucaoQuery->whereBetween('data', [$evolStart->toDateString(), $evolEnd->toDateString()]);

            $multiMeses = $evolucaoQuery
                ->selectRaw("TO_CHAR(data, 'YYYY-MM') as month_key, tipo, SUM(valor) as total")
                ->groupBy('month_key', 'tipo')
                ->get();

            $evolData = [];
            $tempDate = clone $evolStart;
            while ($tempDate <= $evolEnd) {
                $key = $tempDate->format('Y-m');
                $evolData[$key] = [
                    'label' => $tempDate->translatedFormat('M Y'),
                    'receita' => 0.0,
                    'despesa' => 0.0,
                    'saldo' => 0.0
                ];
                $tempDate->addMonth();
            }

            foreach ($multiMeses as $row) {
                if (isset($evolData[$row->month_key])) {
                    $evolData[$row->month_key][$row->tipo] = (float) $row->total;
                }
            }

            foreach ($evolData as &$ed) {
                $ed['saldo'] = $ed['receita'] - $ed['despesa'];
            }

            $recentesQuery = $query
                ->orderBy('data', 'desc')
                ->orderBy('id', 'desc')
                ->select(['id', 'descricao', 'categoria', 'tipo', 'valor', 'data', 'forma_pagamento']);

            if (empty($filtros)) {
                $recentes = $recentesQuery->take(5)->get();
            } else {
                $recentes = $recentesQuery->take($limitRecent)->get();
            }

            return [
                'receita' => (float) $receita,
                'despesa' => (float) $despesa,
                'saldo' => (float) $saldo,
                'atividades_recentes' => $recentes,
                'periodo_label' => $periodo_label,
                'data_inicio' => $data_inicio,
                'data_fim' => $data_fim,
                'evolucao_mensal' => array_values($evolData)
            ];
        };

        if (!empty($filtros)) {
            return $calc();
        }

        $metaKey = "user_summary_keys_{$workspaceId}";
        $existingKeys = cache()->get($metaKey, []);
        if (!in_array($cacheKey, $existingKeys)) {
            $existingKeys[] = $cacheKey;
            cache()->put($metaKey, $existingKeys, now()->addHours(2));
        }

        return cache()->remember($cacheKey, now()->addMinutes(10), $calc);
    }
}
