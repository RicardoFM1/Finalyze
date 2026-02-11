<?php

namespace App\Servicos\Checkout;

use App\Models\Assinatura;

class CancelarPagamentoCheckout
{
    public function executar()
    {
        $usuario = auth()->user();
        if (!$usuario) {
            throw new \Exception('Usuário não autenticado', 401);
        }

        $affected = Assinatura::where('user_id', $usuario->id)
            ->where('status', 'pending')
            ->update(['status' => 'cancelled']);

        if ($affected > 0) {
            return true;
        }

        return false;
    }
}
