<?php

namespace App\Servicos\Lembretes;

use App\Models\Lembrete;

class ListarLembretes
{
    public function executar($usuarioId)
    {
        return Lembrete::withTrashed()
            ->where('usuario_id', $usuarioId)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
