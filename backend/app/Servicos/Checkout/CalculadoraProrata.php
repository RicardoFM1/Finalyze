<?php

namespace App\Servicos\Checkout;

use App\Models\Assinatura;
use Illuminate\Support\Carbon;

class CalculadoraProrata
{
    /**
     * Calcula o crédito disponível baseado no tempo restante da assinatura atual.
     * 
     * @param Assinatura $assinatura
     * @return float Valor em reais
     */
    public function calcularCredito(Assinatura $assinatura)
    {
        if ($assinatura->status !== 'active' || !$assinatura->termina_em || !$assinatura->inicia_em) {
            return 0.0;
        }

        $agora = now();
        $fim = Carbon::parse($assinatura->termina_em);
        $inicio = Carbon::parse($assinatura->inicia_em);

        if ($agora->greaterThanOrEqualTo($fim)) {
            return 0.0;
        }

        $totalDias = $inicio->diffInDays($fim);
        if ($totalDias <= 0) return 0.0;

        $diasRestantes = $agora->diffInDays($fim, false);
        if ($diasRestantes <= 0) return 0.0;

        // Buscar o valor que o usuário realmente pagou por essa assinatura
        // Primeiro tenta o histórico de pagamento vinculado a esta assinatura específica
        $ultimoPagamento = $assinatura->usuario->historicosPagamento()
            ->where('status', 'paid')
            ->where('assinatura_id', $assinatura->id)
            ->where('valor_centavos', '>', 0)
            ->orderBy('pago_em', 'desc')
            ->first();

        // Se não encontrou, usa o valor do plano/período atual da assinatura
        if (!$ultimoPagamento) {
            \Log::info("Prorata: Nenhum pagamento encontrado para assinatura #{$assinatura->id}, usando valor do plano");

            $plano = $assinatura->plano;
            $periodo = $assinatura->periodo;

            if (!$plano || !$periodo) {
                \Log::error("Prorata: Assinatura #{$assinatura->id} sem plano/período associado");
                return 0.0;
            }

            // Busca o valor do pivot plano_periodo
            $valorCentavos = \DB::table('plano_periodo')
                ->where('plano_id', $plano->id)
                ->where('periodo_id', $periodo->id)
                ->value('valor_centavos');

            if (!$valorCentavos) {
                \Log::error("Prorata: Não foi possível determinar valor para plano #{$plano->id} período #{$periodo->id}");
                return 0.0;
            }

            $valorPago = $valorCentavos / 100;
        } else {
            $valorPago = $ultimoPagamento->valor_centavos / 100;
        }

        \Log::info("Calculando Prorrata:", [
            'assinatura_id' => $assinatura->id,
            'totalDias' => $totalDias,
            'diasRestantes' => $diasRestantes,
            'valorPago' => $valorPago,
            'pagamento_id' => $ultimoPagamento->id ?? 'usando_valor_plano'
        ]);

        // Regra de Prorrata simples: (dias_restantes / total_dias) * valor_pago
        $credito = ($diasRestantes / $totalDias) * $valorPago;

        return round($credito, 2);
    }
}
