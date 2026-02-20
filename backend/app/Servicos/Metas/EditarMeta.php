<?php

namespace App\Servicos\Metas;

use Illuminate\Support\Facades\Auth;

class EditarMeta
{
    public function executar(int $id, array $dados)
    {
        $meta = Auth::user()->metas()->findOrFail($id);

        // Se a meta estava concluída, mas agora o objetivo aumentou ou o atual diminuiu
        // e ela não atinge mais 100%, voltamos o status para 'andamento'
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
