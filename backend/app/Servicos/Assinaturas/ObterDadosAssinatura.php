<?php

namespace App\Servicos\Assinaturas;

use App\Models\Assinatura;
use App\Models\HistoricoPagamento;
use Illuminate\Support\Facades\Auth;

class ObterDadosAssinatura
{
    public function executar()
    {
        $user = Auth::user();

        $subscription = Assinatura::where('user_id', $user->id)
            ->with(['plano', 'periodo'])
            ->orderByRaw("CASE WHEN status = 'active' THEN 0 ELSE 1 END")
            ->latest()
            ->first();

        $history = HistoricoPagamento::where('user_id', $user->id)
            ->with(['assinatura.plano', 'assinatura.periodo'])
            ->latest()
            ->get();

        return [
            'assinatura' => $subscription,
            'historico' => $history
        ];
    }
}
