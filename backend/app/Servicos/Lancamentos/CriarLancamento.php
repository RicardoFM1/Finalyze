<?php

namespace App\Servicos\Lancamentos;

use App\Models\Lancamento;
use Illuminate\Support\Facades\Auth;

class CriarLancamento
{
    public function executar(array $dados)
    {
        $usuario = Auth::user();
        Lancamento::validarLimiteLancamentos($usuario->id);

        $lancamento = $usuario->lancamentos()->create($dados);
        cache()->forget("user_summary_{$usuario->id}");
        return $lancamento;
    }
}
