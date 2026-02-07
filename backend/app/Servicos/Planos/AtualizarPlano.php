<?php

namespace App\Servicos\Planos;

use App\Models\Plano;
use Illuminate\Support\Facades\DB;

class AtualizarPlano
{
    public function executar($plan, array $dados)
    {
        $planModel = $plan instanceof Plano ? $plan : Plano::findOrFail($plan);

        return DB::transaction(function () use ($dados, $planModel) {
            $planModel->update($dados);

            if (isset($dados['recursos'])) {
                $planModel->recursos()->sync($dados['recursos']);
            }

            if (isset($dados['periodos'])) {
                $periodos = collect($dados['periodos'])->mapWithKeys(function ($item) {
                    return [$item['id'] => [
                        'valor_centavos' => $item['valor_centavos'],
                        'percentual_desconto' => $item['percentual_desconto'] ?? 0
                    ]];
                })->toArray();

                $planModel->periodos()->sync($periodos);
            }

            return $planModel;
        });
    }
}
