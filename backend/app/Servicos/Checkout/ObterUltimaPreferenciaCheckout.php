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

        $plano = \App\Models\Plano::with(['recursos', 'periodos'])->findOrFail($assinatura->plano_id);
        $periodo = $plano->periodos()->where('periodos.id', $assinatura->periodo_id)->first();

        $calculadora = new CalculadoraProrata();
        $assinaturaAtiva = auth()->user()->assinaturaAtiva();
        $creditos = $assinaturaAtiva ? $calculadora->calcularCredito($assinaturaAtiva) : 0;

        return [
            'id' => $assinatura->mercado_pago_id,
            'plano' => $plano,
            'periodo_id' => $assinatura->periodo_id,
            'valor_centavos' => $periodo ? $periodo->pivot->valor_centavos : 0,
            'creditos_prorrata' => $creditos
        ];
    }
}
