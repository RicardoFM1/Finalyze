<?php

namespace App\Servicos\Planos;

use App\Models\Plano;

class MostrarPlano
{
    public function executar(Plano $plano)
    {
        return $plano->load(['periodos', 'recursos']);
    }
}
