<?php

namespace App\Servicos\Assinaturas;

use App\Models\Assinatura;
use Illuminate\Support\Facades\Auth;

class CancelarAssinatura
{
    public function executar()
    {
        $user = Auth::user();

        $subscription = Assinatura::where('user_id', $user->id)
            ->where('status', 'active')
            ->first();

        if (!$subscription) {
            throw new \Exception('Nenhuma assinatura ativa encontrada', 404);
        }

        $subscription->update([
            'status' => 'cancelled',
            'renovacao_automatica' => false
      
        ]);

        return $subscription;
    }
}

