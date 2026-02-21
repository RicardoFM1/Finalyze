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
                $query->where('categoria', $filtros['categoria']);
            }
            if (!empty($filtros['tipo']) && $filtros['tipo'] !== 'todos') {
                $query->where('tipo', $filtros['tipo']);
            }
            if (!empty($filtros['valor'])) {
                $query->where('valor', $filtros['valor']);
            }

            if (!empty($filtros['data_inicio']) && !empty($filtros['data_fim'])) {
                $query->whereBetween('data', [$filtros['data_inicio'], $filtros['data_fim']]);
            } elseif (!empty($filtros['data'])) {
                $data = $filtros['data'];
                if (str_contains($data, ' to ')) {
                    $parts = explode(' to ', $data);
                    $query->whereBetween('data', [$parts[0], $parts[1] ?? $parts[0]]);
                } else {
                    $query->whereDate('data', $data);
                }
            } else {
                $query->whereMonth('data', now()->month)
                    ->whereYear('data', now()->year);
            }

            $receita = (clone $query)->where('tipo', 'receita')->sum('valor');
            $despesa = (clone $query)->where('tipo', 'despesa')->sum('valor');

            if (!empty($filtros)) {
                $saldo = $receita - $despesa;
                $recentes = $query->orderBy('data', 'desc')->orderBy('id', 'desc')->get();
            } else {
                $totalRec = $usuario->lancamentos()->where('tipo', 'receita')->sum('valor');
                $totalDes = $usuario->lancamentos()->where('tipo', 'despesa')->sum('valor');
                $saldo = $totalRec - $totalDes;
                $recentes = $query->orderBy('data', 'desc')->orderBy('id', 'desc')->take(5)->get();
            }

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

        $metaKey = "user_summary_keys_{$workspaceId}";
        $existingKeys = cache()->get($metaKey, []);
        if (!in_array($cacheKey, $existingKeys)) {
            $existingKeys[] = $cacheKey;
            cache()->put($metaKey, $existingKeys, now()->addHours(2));
        }

        return cache()->remember($cacheKey, now()->addMinutes(10), $calc);
    }
}
