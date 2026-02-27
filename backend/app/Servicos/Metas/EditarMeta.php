<?php

namespace App\Servicos\Metas;

use App\Models\Meta;

class EditarMeta
{
    public function executar(int $id, array $dados)
    {
        $workspaceId = app('workspace_id');
        $meta = Meta::where('id', $id)->where('usuario_id', $workspaceId)->firstOrFail();

        if ($meta->status === 'concluido') {
            $novoValorAtual = $dados['valor_atual'] ?? $meta->valor_atual;
            $novoValorObjetivo = $dados['valor_objetivo'] ?? $meta->valor_objetivo;

            if ($novoValorAtual < $novoValorObjetivo) {
                $dados['status'] = 'andamento';
            }
        }

        $meta->update($dados);
        return $meta;
    }
}
