<?php

namespace App\Servicos\Metas;

use Illuminate\Support\Facades\Auth;

class CriarMeta
{
    public function executar(array $dados)
    {
        return Auth::user()->metas()->create($dados);
    }
}
