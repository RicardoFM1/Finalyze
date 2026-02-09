<?php

namespace App\Servicos\Anotacoes;

use App\Models\Anotacao;

class ListarAnotacoes
{
    public function executar($usuarioId)
    {
        return Anotacao::where('usuario_id', $usuarioId)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
