<?php

namespace App\Servicos\Lembretes;

use App\Models\Lembrete;

class ReativarLembrete
{
    public function executar(int $lembreteId)
    {
        $workspaceId = app('workspace_id');
        $lembrete = Lembrete::withTrashed()
            ->where('usuario_id', $workspaceId)
            ->findOrFail($lembreteId);

        $lembrete->status = 'andamento';
        $lembrete->restore();

        return $lembrete;
    }
}
