<?php

namespace App\Servicos\Planos;

use App\Models\Plano;

class ListarPlanos
{
    public function executar()
    {
        return Plano::with(['periodos', 'recursos'])
            ->whereRaw('ativo IS TRUE')
            ->orderByDesc('created_at')
            ->get();
    }
}
