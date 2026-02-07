<?php

namespace App\Servicos\Planos;

use App\Models\Plano;
use App\Models\Usuario;
use App\Models\Assinatura;

class DeletarPlano
{
    public function executar(Plano $plano)
    {
        $activeCount = Plano::where('ativo', true)->count();

        if ($plano->ativo && $activeCount < 3) {
            throw new \Exception('É necessário manter pelo menos 2 planos ativos.', 422);
        }

        $hasUsuarios = Usuario::where('plano_id', $plano->id)->exists();
        $hasAssinaturas = Assinatura::where('plano_id', $plano->id)->exists();

        if ($hasUsuarios || $hasAssinaturas) {
            throw new \Exception('Este plano não pode ser excluído pois possui usuários ou assinaturas vinculadas.', 422);
        }

        $plano->delete();
    }
}
