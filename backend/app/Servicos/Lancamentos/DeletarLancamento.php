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
        // Limpa todos os caches de resumo do usuário (com qualquer combinação de filtros)
        \App\Servicos\Painel\GerarResumoPainel::limparCacheUsuario($usuario->id);
    }
}
