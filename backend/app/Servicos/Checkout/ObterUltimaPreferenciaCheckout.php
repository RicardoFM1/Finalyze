<?php

namespace App\Servicos\Checkout;

use App\Models\Assinatura;
use App\Models\Plano;

class ObterUltimaPreferenciaCheckout
{
    public function executar()
    {
        $assinatura = Assinatura::where('user_id', auth()->id())
            ->where('status', 'pending')
            ->latest()
            ->firstOrFail();

        $plano = Plano::with(['recursos', 'periodos'])->findOrFail($assinatura->plano_id);
        $periodo = $plano->periodos()->where('periodos.id', $assinatura->periodo_id)->first();

        return [
            'id' => $assinatura->mercado_pago_id,
            'plano' => $plano,
            'periodo_id' => $assinatura->periodo_id,
            'valor_centavos' => $periodo ? $periodo->pivot->valor_centavos : 0
        ];
    }
}
