<?php

namespace App\Servicos\Anotacoes;

use App\Models\Anotacao;
use Illuminate\Support\Facades\Auth;

class ReativarAnotacao
{
    public function executar($id)
    {
        $anotacao = Auth::user()->anotacoes()->findOrFail($id);
        $anotacao->update(['status' => 'andamento']);
        return $anotacao;
    }
}
