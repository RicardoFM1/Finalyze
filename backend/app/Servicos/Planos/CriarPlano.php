<?php

namespace App\Servicos\Planos;

use App\Models\Plano;
use Illuminate\Support\Facades\DB;

class CriarPlano
{
    public function executar(array $dados)
    {
        return DB::transaction(function () use ($dados) {
            $plano = Plano::create($dados);

            if (isset($dados['recursos'])) {
                $plano->recursos()->sync($dados['recursos']);
            }

            if (isset($dados['periodos'])) {
                $periodos = collect($dados['periodos'])->mapWithKeys(function ($item) {
                    return [$item['id'] => [
                        'valor_centavos' => $item['valor_centavos'],
                        'percentual_desconto' => $item['percentual_desconto'] ?? 0
                    ]];
                })->toArray();

                $plano->periodos()->sync($periodos);
            }

            return $plano;
        });
    }
}
