<?php

namespace App\Servicos\Lancamentos;

use Illuminate\Support\Facades\Auth;

class DeletarLancamento
{
    public function executar(int $lancamentoId)
    {
        $usuario = Auth::user();
        $lancamento = $usuario->lancamentos()->findOrFail($lancamentoId);

        if ($lancamento->user_id !== Auth::id()) {
            abort(403, 'Você não tem permissão para editar este lançamento.');
        }

        $lancamento->delete();
        cache()->forget("user_summary_{$usuario->id}");
    }
}
