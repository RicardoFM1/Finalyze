<?php

namespace App\Servicos\Anotacoes;

use App\Models\Anotacao;

class AtualizarAnotacao
{
    public function executar($id, array $dados)
    {
        $anotacao = Anotacao::findOrFail($id);
        $anotacao->update($dados);
        return $anotacao;
    }
}
