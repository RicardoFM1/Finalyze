<?php

namespace App\Servicos\Checkout;

use App\Models\Assinatura;
use App\Models\HistoricoPagamento;
use App\Models\Usuario;
use Illuminate\Support\Facades\Log;

class AtivarPlanoUsuario
{
    public function executar($payment)
    {
        $metadata = (object)$payment->metadata;

        Log::info("Plan Activation Tracking: Processing Payment {$payment->id}", [
            'metadata' => (array)$metadata,
            'external_reference' => $payment->external_reference
        ]);

        $usuarioId = $metadata->user_id ?? null;
        $planoId = $metadata->plano_id ?? null;
        $assinaturaId = $metadata->assinatura_id ?? $payment->external_reference;

        if ($assinaturaId) {
            $assinatura = Assinatura::find($assinaturaId);
            if ($assinatura) {
                $quantidadeDias = $metadata->quantidade_dias ?? 30;
                $newEndsAt = now()->addDays((int)$quantidadeDias);

                $activeSub = Assinatura::where('user_id', $assinatura->user_id)
                    ->where('status', 'active')
                    ->where('id', '!=', $assinatura->id)
                    ->first();

                if ($activeSub && $activeSub->termina_em && $activeSub->termina_em->isFuture()) {
                    $newEndsAt = $activeSub->termina_em->addDays((int)$quantidadeDias);
                    $activeSub->update(['status' => 'cancelled']);
                }

                $assinatura->update([
                    'mercado_pago_id' => $payment->id,
                    'status' => 'active',
                    'inicia_em' => now(),
                    'termina_em' => $newEndsAt
                ]);

                HistoricoPagamento::create([
                    'user_id' => $assinatura->user_id,
                    'assinatura_id' => $assinatura->id,
                    'valor_centavos' => (int)($payment->transaction_amount * 100),
                    'status' => 'paid',
                    'metodo_pagamento' => $payment->payment_method_id,
                    'mercado_pago_id' => (string)$payment->id,
                    'pago_em' => now()
                ]);

                Log::info("Assinatura {$assinaturaId} activated. Ends at: " . $newEndsAt);

                $usuarioId = $assinatura->user_id;
                $planoId = $assinatura->plano_id;
            }
        }

        if ($usuarioId && $planoId) {
            Usuario::where('id', (int)$usuarioId)
                ->update([
                    'plano_id' => (int)$planoId,
                    'updated_at' => now()
                ]);
        }
    }
}
