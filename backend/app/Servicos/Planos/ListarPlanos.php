<?php

namespace App\Servicos\Planos;

use App\Models\Plano;

class ListarPlanos
{
    public function executar()
    {
        return Plano::with(['periodos', 'recursos'])
            ->where('ativo', true)
            ->orderByDesc('created_at')
            ->get();
    }
}
