<?php

namespace App\Servicos\Anotacoes;

use App\Models\Anotacao;

class CriarAnotacao
{
    public function executar(array $dados)
    {
        return Anotacao::create($dados);
    }
}
