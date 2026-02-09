<?php

namespace App\Servicos\Anotacoes;

use App\Models\Anotacao;

class ExcluirAnotacao
{
    public function executar($id)
    {
        $anotacao = Anotacao::findOrFail($id);
        return $anotacao->delete();
    }
}
