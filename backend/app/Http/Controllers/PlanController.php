<?php

namespace App\Http\Controllers;

use App\Models\Plano;
use App\Http\Requests\StorePlanRequest;
use App\Http\Requests\UpdatePlanRequest;
use App\Servicos\Planos\ListarPlanos;
use App\Servicos\Planos\ListarPlanosAdmin;
use App\Servicos\Planos\CriarPlano;
use App\Servicos\Planos\MostrarPlano;
use App\Servicos\Planos\AtualizarPlano;
use App\Servicos\Planos\DeletarPlano;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index(ListarPlanos $servico)
    {
        return $servico->executar();
    }

    public function adminIndex(ListarPlanosAdmin $servico)
    {
        return $servico->executar();
    }

    public function criar(StorePlanRequest $request, CriarPlano $servico)
    {
        $plano = $servico->executar($request->validated());

        return response()->json([
            'message' => 'Plano criado com sucesso!',
            'id' => $plano->id
        ], 201);
    }

    public function mostrar(Plano $plano, MostrarPlano $servico)
    {
        return $servico->executar($plano);
    }

    public function atualizar(UpdatePlanRequest $request, $plan, AtualizarPlano $servico)
    {
        $servico->executar($plan, $request->validated());
        return response()->json(['message' => 'Plano atualizado com sucesso!']);
    }

    public function destruir(Plano $plano, DeletarPlano $servico)
    {
        try {
            $servico->executar($plano);
            return response()->json(['message' => 'Plano excluído com sucesso.'], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], $e->getCode() ?: 422);
        }
    }
}
