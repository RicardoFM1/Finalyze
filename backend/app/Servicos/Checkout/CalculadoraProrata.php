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

        // Pegamos o valor pago no histórico mais recente vinculado a essa assinatura ou do usuário
        $ultimoPagamento = $assinatura->usuario->historicosPagamento()
            ->where('status', 'paid')
            ->where('valor_centavos', '>', 0)
            ->where(function ($query) use ($assinatura) {
                $query->where('assinatura_id', $assinatura->id)
                    ->orWhereNull('assinatura_id');
            })
            ->orderBy('pago_em', 'desc')
            ->first();

        // Se não achou nada vinculado, pega o último pagamento pago qualquer do usuário
        if (!$ultimoPagamento) {
            $ultimoPagamento = $assinatura->usuario->historicosPagamento()
                ->where('status', 'paid')
                ->where('valor_centavos', '>', 0)
                ->orderBy('pago_em', 'desc')
                ->first();
        }

        if (!$ultimoPagamento) {
            \Log::warning("Prorata: Nenhum pagamento pago encontrado para o usuário #{$assinatura->user_id}");
            return 0.0;
        }

        $valorPago = $ultimoPagamento->valor_centavos / 100;

        \Log::info("Calculando Prorrata:", [
            'totalDias' => $totalDias,
            'diasRestantes' => $diasRestantes,
            'valorPago' => $valorPago,
            'pagamento_id' => $ultimoPagamento->id
        ]);

        // Regra de Prorrata simples: (dias_restantes / total_dias) * valor_pago
        $credito = ($diasRestantes / $totalDias) * $valorPago;

        return round($credito, 2);
    }
}
