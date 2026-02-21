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
            'external_reference' => $payment->external_reference ?? null
        ]);

        $usuarioId = $metadata->user_id ?? null;
        $planoId = $metadata->plano_id ?? null;
        $assinaturaId = $metadata->assinatura_id ?? $payment->external_reference;

        if ($assinaturaId) {
            $assinatura = Assinatura::find($assinaturaId);
            if ($assinatura) {
                // Verifica se este pagamento já foi processado como PAGO
                $historicoPago = HistoricoPagamento::where('mercado_pago_id', (string)$payment->id)
                    ->where('status', 'paid')
                    ->exists();

                if ($historicoPago) {
                    Log::info("Pagamento {$payment->id} já foi ativado anteriormente.");
                } else {
                    // Se o metadata estiver vazio (comum em renovações automáticas do MP), 
                    // buscamos a duração correta no periodo relacionado à assinatura
                    $quantidadeDias = $metadata->quantidade_dias ?? $assinatura->periodo->quantidade_dias ?? 30;
                    $creditosProrrata = $metadata->creditos_prorrata ?? 0;

                    // Busca assinatura ATIVA atual (pode ser a própria $assinatura se estiver sendo renovada)
                    $activeSub = Assinatura::where('user_id', $assinatura->user_id)
                        ->where('status', 'active')
                        ->where('termina_em', '>', now())
                        ->orderBy('termina_em', 'desc')
                        ->first();

                    // Se renovação (mesmo plano), soma a partir do término atual. 
                    // Se mudança ou nova, inicia agora.
                    $isSamePlan = $activeSub && $activeSub->plano_id == $assinatura->plano_id;

                    // Se for a mesma assinatura (renovação da mesma assinatura recorrente):
                    if ($activeSub && $activeSub->id === $assinatura->id) {
                        $baseDate = $activeSub->termina_em->isFuture() ? $activeSub->termina_em : now();
                    } else {
                        $baseDate = ($isSamePlan && $activeSub->termina_em) ? $activeSub->termina_em : now();
                    }

                    $newEndsAt = $baseDate->copy()->addDays((int)$quantidadeDias);

                    // Se for uma assinatura DIFERENTE da ativa (upgrade/mudança), cancela a antiga
                    if ($activeSub && $activeSub->id !== $assinatura->id) {
                        $activeSub->update(['status' => 'cancelled']);
                        Log::info("Assinatura antiga #{$activeSub->id} substituída pela nova #{$assinatura->id}.");
                    }

                    // Cancelar qualquer outra tentativa pendente para este usuário (limpeza)
                    Assinatura::where('user_id', $assinatura->user_id)
                        ->where('status', 'pending')
                        ->where('id', '!=', $assinatura->id)
                        ->update(['status' => 'cancelled']);

                    $assinatura->update([
                        'mercado_pago_id' => $payment->id,
                        'status' => 'active',
                        'inicia_em' => $assinatura->inicia_em ?? now(),
                        'termina_em' => $newEndsAt
                    ]);

                    // Busca se já existe um registro pendente para atualizar, ou cria um novo
                    HistoricoPagamento::updateOrCreate(
                        ['mercado_pago_id' => (string)$payment->id],
                        [
                            'user_id' => $assinatura->user_id,
                            'assinatura_id' => $assinatura->id,
                            'valor_centavos' => (int)($payment->transaction_amount * 100),
                            'status' => 'paid',
                            'metodo_pagamento' => $payment->payment_method_id,
                            'pago_em' => now()
                        ]
                    );

                    Log::info("Assinatura {$assinaturaId} activated. Ends at: " . $newEndsAt);
                }

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
