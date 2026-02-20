<?php

namespace App\Servicos\Painel;

use Illuminate\Support\Facades\Auth;

class GerarResumoPainel
{
    public function executar()
    {
        $usuario = Auth::user();
        $cacheKey = "user_summary_{$usuario->id}";

        return cache()->remember($cacheKey, now()->addMinutes(10), function () use ($usuario) {
            $receita = $usuario->lancamentos()->where('tipo', 'receita')->sum('valor');
            $despesa = $usuario->lancamentos()->where('tipo', 'despesa')->sum('valor');
            $saldo = $receita - $despesa;

            $recentes = $usuario->lancamentos()->latest()->take(5)->get();

            return [
                'receita' => (float) $receita,
                'despesa' => (float) $despesa,
                'saldo' => (float) $saldo,
                'atividades_recentes' => $recentes
            ];
        });
    }
}
