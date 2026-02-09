<?php

namespace App\Servicos\Assinaturas;

use App\Models\Assinatura;
use Illuminate\Support\Facades\Auth;

class AlternarRenovacaoAutomatica
{
    public function executar(bool $ativo)
    {
        $user = Auth::user();
        $subscription = Assinatura::where('user_id', $user->id)
            ->where('status', 'active')
            ->first();

        if (!$subscription) {
            throw new \Exception('Nenhuma assinatura ativa encontrada', 404);
        }

        $subscription->update(['renovacao_automatica' => $ativo]);

        return $subscription;
    }
}
