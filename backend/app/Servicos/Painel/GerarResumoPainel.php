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

        $filtrosHash = md5(json_encode($filtros));
        $cacheKey = "user_summary_{$workspaceId}_{$filtrosHash}";

        $calc = function () use ($usuario, $filtros) {
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

            // If no filters at all, limit to 5, otherwise show filtered
            if (empty($filtros)) {
                $recentes = $query->orderBy('data', 'desc')->orderBy('id', 'desc')->take(5)->get();
            } else {
                $recentes = $query->orderBy('data', 'desc')->orderBy('id', 'desc')->get();
            }

            return [
                'receita' => (float) $receita,
                'despesa' => (float) $despesa,
                'saldo' => (float) $saldo,
                'atividades_recentes' => $recentes,
                'periodo_label' => $periodo_label,
                'data_inicio' => $data_inicio,
                'data_fim' => $data_fim
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
