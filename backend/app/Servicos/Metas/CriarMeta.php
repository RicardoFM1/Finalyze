<?php

namespace App\Servicos\Metas;

use Illuminate\Support\Facades\Auth;

class CriarMeta
{
    public function executar(array $dados)
    {
        $dados['usuario_id'] = app('workspace_id');
        return \App\Models\Meta::create($dados);
    }
}
