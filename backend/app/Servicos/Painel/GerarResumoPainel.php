<?php

namespace App\Servicos\Painel;

use Illuminate\Support\Facades\Auth;

class GerarResumoPainel
{
    public function executar()
    {
        $usuario = Auth::user();

        $receita = $usuario->lancamentos()->where('tipo', 'receita')->sum('valor');
        $despesa = $usuario->lancamentos()->where('tipo', 'despesa')->sum('valor');
        $saldo = $receita - $despesa;

        $recentes = $usuario->lancamentos()->latest()->take(5)->get();

        return [
            'receita' => $receita,
            'despesa' => $despesa,
            'saldo' => $saldo,
            'atividades_recentes' => $recentes
        ];
    }
}
