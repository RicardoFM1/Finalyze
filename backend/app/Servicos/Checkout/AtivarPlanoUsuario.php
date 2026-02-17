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
                // Verifica se este pagamento JÁ foi processado para evitar duplicidade de dias
                $historicoExiste = HistoricoPagamento::where('mercado_pago_id', (string)$payment->id)->exists();

                if ($historicoExiste) {
                    Log::info("Pagamento {$payment->id} já processado anteriormente. Ignorando atualização de dias.");
                } else {
                    $quantidadeDias = $metadata->quantidade_dias ?? 30;
                    $creditosProrrata = $metadata->creditos_prorrata ?? 0;

                    // Busca a assinatura ativa ATUAL do usuário
                    $activeSub = Assinatura::where('user_id', $assinatura->user_id)
                        ->where('status', 'active')
                        ->where('termina_em', '>', now())
                        ->orderBy('termina_em', 'desc')
                        ->first();

                    // Lógica de Vencimento:
                    // 1. Se for o MESMO plano: Acumulamos (base = data de término atual)
                    // 2. Se for um NOVO plano: Resetamos (base = agora) -> O valor restante foi convertido em crédito

                    $isSamePlan = $activeSub && $activeSub->plano_id == $assinatura->plano_id;

                    $baseDate = ($isSamePlan && $activeSub->termina_em) ? $activeSub->termina_em : now();
                    $newEndsAt = $baseDate->copy()->addDays((int)$quantidadeDias);

                    // Se mudou de plano, cancelamos a assinatura antiga
                    if ($activeSub && $activeSub->id !== $assinatura->id && !$isSamePlan) {
                        $activeSub->update(['status' => 'cancelled']);
                        Log::info("Usuário #{$usuarioId} mudou de plano. Assinatura antiga #{$activeSub->id} cancelada.");
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
                }

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
