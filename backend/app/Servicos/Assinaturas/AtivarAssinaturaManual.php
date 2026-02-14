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

        // Se não informar período, tenta pegar o primeiro disponível do plano
        if (!$periodoId) {
            $periodo = $plano->periodos()->first();
            $periodoId = $periodo ? $periodo->id : null;
        }

        // Calcula data de término (padrão 30 dias se não houver lógica complexa de período ainda)
        // Você pode expandir isso para usar a duração real do período se houver essa coluna
        $terminaEm = now()->addDays(30);

        // Inativa assinaturas atuais
        Assinatura::where('user_id', $usuarioId)
            ->where('status', 'active')
            ->update(['status' => 'cancelled']);

        // Cria nova assinatura manual
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

        // Atualiza o plano no perfil do usuário
        $usuario->update([
            'plano_id' => $planoId
        ]);

        return $assinatura;
    }
}
