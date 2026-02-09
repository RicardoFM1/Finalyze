<?php

namespace App\Servicos\Metas;

use Illuminate\Support\Facades\Auth;

class DeletarMeta
{
    public function executar(int $id)
    {
        $meta = Auth::user()->metas()->findOrFail($id);
        $meta->delete();
    }
}
