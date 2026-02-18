<?php

namespace App\Servicos\Checkout;

use App\Models\Assinatura;

class CancelarPagamentoCheckout
{
    public function executar()
    {
        $usuario = \Illuminate\Support\Facades\Auth::user();
        if (!$usuario) {
            throw new \Exception('Usuário não autenticado', 401);
        }

        $assinatura = Assinatura::where('user_id', $usuario->id)
            ->where('status', 'pending')
            ->latest()
            ->first();

        if ($assinatura) {
            $assinatura->update(['status' => 'cancelled']);
            return true;
        }

        return false;
    }
}
