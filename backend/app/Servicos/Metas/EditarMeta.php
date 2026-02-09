<?php

namespace App\Servicos\Metas;

use Illuminate\Support\Facades\Auth;

class EditarMeta
{
    public function executar(int $id, array $dados)
    {
        $meta = Auth::user()->metas()->findOrFail($id);
        $meta->update($dados);
        return $meta;
    }
}
