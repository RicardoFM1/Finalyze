<?php

namespace App\Servicos\Lembretes;

use App\Models\Lembrete;

class AtualizarLembrete
{
    public function executar(int $lembreteId, array $dados)
    {
        $workspaceId = app('workspace_id');
        $lembrete = Lembrete::where('usuario_id', $workspaceId)->findOrFail($lembreteId);
        $lembrete->update($dados);
        return $lembrete;
    }
}
