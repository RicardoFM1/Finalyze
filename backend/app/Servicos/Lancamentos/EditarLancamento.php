<?php

namespace App\Servicos\Lancamentos;

use Illuminate\Support\Facades\Auth;

class EditarLancamento
{
    public function executar(int $lancamentoId, array $dados)
    {
        $usuario = Auth::user();
        $lancamento = $usuario->lancamentos()->findOrFail($lancamentoId);

        if ($lancamento->user_id !== Auth::id()) {
            abort(403, 'Você não tem permissão para editar este lançamento.');
        }

        $lancamento->update($dados);
        return $lancamento;
    }
}
