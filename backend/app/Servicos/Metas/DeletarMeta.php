<?php

namespace App\Servicos\Metas;

use App\Models\Meta;

class DeletarMeta
{
    public function executar(int $id)
    {
        $workspaceId = app('workspace_id');
        $meta = Meta::where('id', $id)->where('usuario_id', $workspaceId)->firstOrFail();

        if ($meta->status === 'inativo') {
            return;
        }

        $meta->update(['status' => 'inativo']);
    }
}
