<?php

namespace App\Servicos\Lembretes;

use App\Models\Lembrete;

class ExcluirLembrete
{
    public function executar(int $lembreteId)
    {
        $workspaceId = app('workspace_id');
        $lembrete = Lembrete::withTrashed()
            ->where('usuario_id', $workspaceId)
            ->findOrFail($lembreteId);

        if ($lembrete->status === 'inativo' || $lembrete->trashed()) {
            return; // Already done
        }

        $lembrete->status = 'inativo';
        $lembrete->save();

        $lembrete->delete();
    }
}
