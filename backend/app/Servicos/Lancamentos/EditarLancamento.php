<?php

namespace App\Servicos\Lancamentos;

use App\Models\Usuario;
use App\Servicos\Painel\GerarResumoPainel;

class EditarLancamento
{
    public function executar(int $lancamentoId, array $dados)
    {
        $workspaceId = app('workspace_id');
        $usuario = Usuario::findOrFail($workspaceId);

        $lancamento = $usuario->lancamentos()->findOrFail($lancamentoId);

        $lancamento->update($dados);

        GerarResumoPainel::limparCacheUsuario($workspaceId);

        return $lancamento;
    }
}
