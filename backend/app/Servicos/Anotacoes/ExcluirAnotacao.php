<?php

namespace App\Servicos\Anotacoes;

use App\Models\Anotacao;
use Illuminate\Support\Facades\Auth;

class ExcluirAnotacao
{
    public function executar($id)
    {
        $anotacao = Auth::user()->anotacoes()->findOrFail($id);
        return $anotacao->update(['status' => 'inativo']);
    }
}
