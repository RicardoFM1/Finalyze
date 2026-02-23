<?php

namespace App\Servicos\Metas;

use Illuminate\Support\Facades\Auth;
use App\Models\Usuario;

class ListarMetas
{
    public function executar()
    {
        $workspaceId = app('workspace_id');
        $usuario = Usuario::findOrFail($workspaceId);

        $receita = $usuario->lancamentos()->where('tipo', 'receita')->sum('valor');
        $despesa = $usuario->lancamentos()->where('tipo', 'despesa')->sum('valor');
        $saldo = (float)($receita - $despesa);

        $metas = $usuario->metas()->orderBy('created_at', 'desc')->get();

        $metas->map(function ($meta) use ($saldo) {
            if ($meta->tipo === 'financeira') {
                $meta->valor_atual = $saldo;
            }
            return $meta;
        });

        return $metas;
    }
}
