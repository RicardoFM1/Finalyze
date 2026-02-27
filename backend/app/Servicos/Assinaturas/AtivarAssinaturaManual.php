<?php

namespace App\Servicos\Assinaturas;

use App\Models\Assinatura;
use App\Models\Usuario;
use App\Models\Plano;
use App\Models\Periodo;

class AtivarAssinaturaManual
{
    public function executar(int $usuarioId, int $planoId, int $periodoId = null)
    {
        $usuario = Usuario::findOrFail($usuarioId);
        $plano = Plano::findOrFail($planoId);

        if (!$periodoId) {
            $periodo = $plano->periodos()->first();
            $periodoId = $periodo ? $periodo->id : null;
        }

        $terminaEm = now()->addDays(30);

        Assinatura::where('user_id', $usuarioId)
            ->where('status', 'active')
            ->update(['status' => 'cancelled']);

        $assinatura = Assinatura::create([
            'user_id' => $usuarioId,
            'plano_id' => $planoId,
            'periodo_id' => $periodoId,
            'mercado_pago_id' => 'MANUAL_' . uniqid(),
            'status' => 'active',
            'inicia_em' => now(),
            'termina_em' => $terminaEm,
            'renovacao_automatica' => false
        ]);

        $usuario->update([
            'plano_id' => $planoId
        ]);

        $valor = 0;
        if ($periodoId) {
            $periodo = Periodo::find($periodoId);
            if ($periodo) {
                
                $pivot = \DB::table('plano_periodo')
                    ->where('plano_id', $planoId)
                    ->where('periodo_id', $periodoId)
                    ->first();
                $valor = $pivot ? $pivot->valor_centavos : 0;
            }
        }

        \App\Models\HistoricoPagamento::create([
            'user_id' => $usuarioId,
            'assinatura_id' => $assinatura->id,
            'valor_centavos' => $valor,
            'status' => 'paid',
            'metodo_pagamento' => 'manual_system',
            'mercado_pago_id' => (string)$assinatura->mercado_pago_id,
            'pago_em' => now()
        ]);

        return $assinatura;
    }
}
