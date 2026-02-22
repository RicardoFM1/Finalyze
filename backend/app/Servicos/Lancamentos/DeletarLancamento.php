<?php

namespace App\Servicos\Lancamentos;

use App\Models\Usuario;
use App\Servicos\Painel\GerarResumoPainel;

class DeletarLancamento
{
    public function executar(int $lancamentoId)
    {
        $workspaceId = app('workspace_id');
        $usuario = Usuario::findOrFail($workspaceId);

        $lancamento = $usuario->lancamentos()->find($lancamentoId);

        if ($lancamento) {
            $lancamento->delete();
            GerarResumoPainel::limparCacheUsuario($workspaceId);
        }
    }
}
