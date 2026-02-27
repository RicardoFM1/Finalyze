<?php

namespace App\Servicos\Metas;

use Illuminate\Support\Facades\Auth;
use App\Models\Usuario;

class ListarMetas
{
    public function executar(array $filtros = [])
    {
        $workspaceId = app('workspace_id');
        $usuario = Usuario::findOrFail($workspaceId);

        $receita = $usuario->lancamentos()->where('tipo', 'receita')->sum('valor');
        $despesa = $usuario->lancamentos()->where('tipo', 'despesa')->sum('valor');
        $saldo = (float)($receita - $despesa);

        $query = $usuario->metas()->orderBy('created_at', 'desc');
        if (($filtros['status'] ?? null) === 'ativos') {
            $query->where('status', '!=', 'inativo');
        }

        if (!empty($filtros['limit'])) {
            $limit = max(1, min(100, (int) $filtros['limit']));
            $query->limit($limit);
        }

        $metas = $query->get();

        $metas->map(function ($meta) use ($saldo) {
            if ($meta->tipo === 'financeira') {
                $meta->valor_atual = $saldo;
            }
            return $meta;
        });

        return $metas;
    }
}
