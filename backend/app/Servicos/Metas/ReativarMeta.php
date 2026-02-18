<?php

namespace App\Servicos\Metas;

use Illuminate\Support\Facades\Auth;

class ReativarMeta
{
    public function executar(int $id)
    {
        $meta = Auth::user()->metas()->findOrFail($id);
        $meta->update(['status' => 'andamento']);
        return $meta;
    }
}
