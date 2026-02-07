<?php

namespace App\Servicos\Metas;

use Illuminate\Support\Facades\Auth;

class ListarMetas
{
    public function executar()
    {
        return Auth::user()->metas()->latest()->get();
    }
}
