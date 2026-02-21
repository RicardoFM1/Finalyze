<?php

namespace App\Servicos\Lembretes;

use App\Models\Lembrete;

class CriarLembrete
{
    public function executar(array $dados)
    {
        return Lembrete::create($dados);
    }
}
