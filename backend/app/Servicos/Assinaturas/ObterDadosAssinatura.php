<?php

namespace App\Servicos\Assinaturas;

use App\Models\Assinatura;
use App\Models\Faturamento;
use Illuminate\Support\Facades\Auth;

class ObterDadosAssinatura
{
    public function executar()
    {
        $user = Auth::user();

        $subscription = Assinatura::where('user_id', $user->id)
            ->with(['plano', 'periodo'])
            ->latest()
            ->first();

        $history = Faturamento::where('user_id', $user->id)
            ->with(['assinatura.plano', 'assinatura.periodo'])
            ->latest()
            ->get();

        return [
            'assinatura' => $subscription,
            'historico' => $history
        ];
    }
}
