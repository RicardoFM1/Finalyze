<?php

namespace App\Servicos\Checkout;

use App\Models\Assinatura;
use App\Models\Faturamento;
use App\Models\HistoricoPagamento;
use App\Models\Usuario;
use Illuminate\Support\Facades\Log;

class AtivarPlanoUsuario
{
    public function executar($payment)
    {
        $metadata = (object) $payment->metadata;

        Log::info("Plan Activation Tracking: Processing Payment {$payment->id}", [
            'metadata' => (array) $metadata,
            'external_reference' => $payment->external_reference ?? null
        ]);

        $usuarioId = $metadata->user_id ?? null;
        $planoId = $metadata->plano_id ?? null;
        $assinaturaId = $metadata->assinatura_id ?? $payment->external_reference;

        if ($assinaturaId) {
            $assinatura = Assinatura::find($assinaturaId);

            if ($assinatura) {
                $historicoPago = HistoricoPagamento::where('mercado_pago_id', (string) $payment->id)
                    ->where('status', 'paid')
                    ->exists();

                if ($historicoPago) {
                    Log::info("Pagamento {$payment->id} já foi ativado anteriormente.");
                } else {
                    $quantidadeDias = (int) ($metadata->quantidade_dias ?? 30);

                    $activeSub = Assinatura::where('user_id', $assinatura->user_id)
                        ->where('status', 'active')
                        ->where('termina_em', '>', now())
                        ->orderBy('termina_em', 'desc')
                        ->first();

                    $isSamePlan = $activeSub && $activeSub->plano_id == $assinatura->plano_id;
                    $baseDate = ($isSamePlan && $activeSub->termina_em) ? $activeSub->termina_em : now();
                    $newEndsAt = $baseDate->copy()->addDays($quantidadeDias);

                    if ($activeSub && $activeSub->id !== $assinatura->id) {
                        $activeSub->update(['status' => 'cancelled']);
                        Log::info("Assinatura antiga #{$activeSub->id} substituída pela nova #{$assinatura->id}.");
                    }

                    Assinatura::where('user_id', $assinatura->user_id)
                        ->where('status', 'pending')
                        ->where('id', '!=', $assinatura->id)
                        ->update(['status' => 'cancelled']);

                    $assinatura->update([
                        'mercado_pago_id' => $payment->id,
                        'status' => 'active',
                        'inicia_em' => now(),
                        'termina_em' => $newEndsAt
                    ]);

                    HistoricoPagamento::updateOrCreate(
                        ['mercado_pago_id' => (string) $payment->id],
                        [
                            'user_id' => $assinatura->user_id,
                            'assinatura_id' => $assinatura->id,
                            'valor_centavos' => (int) ($payment->transaction_amount * 100),
                            'status' => 'paid',
                            'metodo_pagamento' => $payment->payment_method_id,
                            'pago_em' => now()
                        ]
                    );

                    Faturamento::updateOrCreate(
                        ['mercado_pago_id' => (string) $payment->id],
                        [
                            'user_id' => $assinatura->user_id,
                            'assinatura_id' => $assinatura->id,
                            'valor_centavos' => (int) ($payment->transaction_amount * 100),
                            'status' => 'paid',
                            'metodo_pagamento' => $payment->payment_method_id,
                            'pago_em' => now()
                        ]
                    );

                    Log::info("Assinatura {$assinaturaId} activated. Ends at: {$newEndsAt}");
                }

                $usuarioId = $assinatura->user_id;
                $planoId = $assinatura->plano_id;
            }
        }

        if ($usuarioId && $planoId) {
            Usuario::where('id', (int) $usuarioId)
                ->update([
                    'plano_id' => (int) $planoId,
                    'updated_at' => now()
                ]);
        }
    }
}
