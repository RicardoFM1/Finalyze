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

        $pendenteIds = Assinatura::where('user_id', $usuario->id)
            ->where('status', 'pending')
            ->pluck('id');

        if ($pendenteIds->isNotEmpty()) {
            \Illuminate\Support\Facades\Log::info("Cancelando tentativas de pagamento pendentes para usuário #{$usuario->id}", ['ids' => $pendenteIds]);

            Assinatura::whereIn('id', $pendenteIds)->update(['status' => 'cancelled']);

            \App\Models\HistoricoPagamento::whereIn('assinatura_id', $pendenteIds)
                ->where('status', 'pending')
                ->update(['status' => 'cancelled']);

            return true;
        }

        return false;
    }
}
