<?php

namespace App\Servicos\Lancamentos;

use App\Models\Lancamento;
use App\Models\Usuario;
use App\Servicos\Painel\GerarResumoPainel;

class CriarLancamento
{
    public function executar(array $dados)
    {
        $workspaceId = app('workspace_id');
        $usuario = Usuario::findOrFail($workspaceId);

        Lancamento::validarLimiteLancamentos($usuario->id);

        $lancamento = $usuario->lancamentos()->create($dados);

        // Limpa todos os caches de resumo do usuário do workspace
        GerarResumoPainel::limparCacheUsuario($workspaceId);

        return $lancamento;
    }
}
