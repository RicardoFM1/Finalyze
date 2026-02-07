<?php

namespace App\Servicos\Planos;

use App\Models\Plano;

class ListarPlanosAdmin
{
    public function executar()
    {
        return Plano::with(['periodos', 'recursos'])
            ->orderByDesc('created_at')
            ->get();
    }
}
