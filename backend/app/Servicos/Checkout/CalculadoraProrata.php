<?php

namespace App\Servicos\Checkout;

use App\Models\Assinatura;
use Illuminate\Support\Carbon;

class CalculadoraProrata
{
    
    public function calcularCredito(Assinatura $assinatura)
    {
        
        \Log::info("🔄 CalculadoraProrata v2.0 - FIXED VERSION", ['assinatura_id' => $assinatura->id]);

        if ($assinatura->status !== 'active' || !$assinatura->termina_em || !$assinatura->inicia_em) {
            return 0.0;
        }

        $agora = now();
        $fim = Carbon::parse($assinatura->termina_em);
        $inicio = Carbon::parse($assinatura->inicia_em);

        if ($agora->greaterThanOrEqualTo($fim)) {
            return 0.0;
        }

        $totalSegundos = $inicio->diffInSeconds($fim);
        if ($totalSegundos <= 0) return 0.0;

        $segundosRestantes = $agora->diffInSeconds($fim, false);
        if ($segundosRestantes <= 0) return 0.0;

        $pagamentos = $assinatura->usuario->historicosPagamento()
            ->where('status', 'paid')
            ->where('assinatura_id', $assinatura->id)
            ->get();

        $totalPagoEmCentavos = $pagamentos->sum('valor_centavos');

        if ($totalPagoEmCentavos == 0) {
            $valorCentavos = \DB::table('plano_periodo')
                ->where('plano_id', $assinatura->plano_id)
                ->where('periodo_id', $assinatura->periodo_id)
                ->value('valor_centavos') ?? 0;

            $valorPago = $valorCentavos / 100;
        } else {
            $valorPago = $totalPagoEmCentavos / 100;
        }

        $credito = ($segundosRestantes / $totalSegundos) * $valorPago;

        return round($credito, 2);
    }
}
