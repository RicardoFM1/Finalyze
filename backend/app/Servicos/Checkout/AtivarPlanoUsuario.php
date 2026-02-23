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
        // 1. Sempre registrar ou atualizar o histórico de pagamento
        $this->registrarHistorico($payment);

        // 2. Se o status for aprovado, ativar o plano
        if ($payment->status === 'approved') {
            $this->ativar($payment);
        }
    }

    private function registrarHistorico($payment)
    {
        $metadata = $payment->metadata ?? [];
        if (is_array($metadata)) {
            $metadata = (object)$metadata;
        }

        $assinaturaId = $metadata->assinatura_id ?? $metadata->assinaturaId ?? $payment->external_reference;

        if (!$assinaturaId) return;

        $assinatura = Assinatura::find($assinaturaId);
        if (!$assinatura) return;

        // Tenta encontrar por payment ID real primeiro
        $historico = HistoricoPagamento::where('mercado_pago_id', (string)$payment->id)->first();

        // Se não achou, tenta achar o registro de "preferência" daquela assinatura para converter
        if (!$historico) {
            $historico = HistoricoPagamento::where('assinatura_id', $assinatura->id)
                ->where('metodo_pagamento', 'preferência')
                ->first();
        }

        if ($historico) {
            $historico->update([
                'mercado_pago_id' => (string)$payment->id,
                'status' => $payment->status,
                'metodo_pagamento' => $payment->payment_method_id ?? $historico->metodo_pagamento,
                'pago_em' => ($payment->status === 'approved') ? now() : $historico->pago_em
            ]);
        } else {
            HistoricoPagamento::create([
                'mercado_pago_id' => (string)$payment->id,
                'user_id' => $assinatura->user_id,
                'assinatura_id' => $assinatura->id,
                'valor_centavos' => (int)($payment->transaction_amount * 100),
                'status' => $payment->status,
                'metodo_pagamento' => $payment->payment_method_id,
                'pago_em' => ($payment->status === 'approved') ? now() : null
            ]);
        }
    }

    private function ativar($payment)
    {
        $metadata = $payment->metadata ?? [];
        if (is_array($metadata)) {
            $metadata = (object)$metadata;
        }

        $assinaturaId = $metadata->assinatura_id ?? $metadata->assinaturaId ?? $payment->external_reference;

        if (!$assinaturaId) return;

        $assinatura = Assinatura::find($assinaturaId);
        if (!$assinatura) return;

        $quantidadeDias = $metadata->quantidade_dias ?? $assinatura->periodo->quantidade_dias ?? 30;

        // Busca assinatura ATIVA atual
        $activeSub = Assinatura::where('user_id', $assinatura->user_id)
            ->where('status', 'active')
            ->where('termina_em', '>', now())
            ->orderBy('termina_em', 'desc')
            ->first();

        $isSamePlan = $activeSub && $activeSub->plano_id == $assinatura->plano_id;

        if ($activeSub && $activeSub->id === $assinatura->id) {
            $baseDate = $activeSub->termina_em->isFuture() ? $activeSub->termina_em : now();
        } else {
            $baseDate = ($isSamePlan && $activeSub->termina_em) ? $activeSub->termina_em : now();
        }

        $newEndsAt = $baseDate->copy()->addDays((int)$quantidadeDias);

        // Cancela assinatura antiga se for diferente
        if ($activeSub && $activeSub->id !== $assinatura->id) {
            $activeSub->update(['status' => 'cancelled']);
        }

        // Limpa outras pendentes
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

        Usuario::where('id', $assinatura->user_id)
            ->update([
                'plano_id' => $assinatura->plano_id,
                'updated_at' => now()
            ]);

        Log::info("Assinatura {$assinaturaId} ativada via sistema.");
    }
}
