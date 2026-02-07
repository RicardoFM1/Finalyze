<?php

namespace App\Servicos\Lancamentos;

use Illuminate\Support\Facades\Auth;

class ListarLancamentos
{
    public function executar()
    {
        return Auth::user()->lancamentos()->latest()->get();
    }
}
