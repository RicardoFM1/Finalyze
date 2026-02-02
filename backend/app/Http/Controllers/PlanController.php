<?php

namespace App\Http\Controllers;

use App\Models\Plano;
use App\Models\Usuario;
use App\Models\Assinatura;
use App\Http\Requests\StorePlanRequest;
use App\Http\Requests\UpdatePlanRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlanController extends Controller
{
    public function index()
    {
        return Plano::with(['periodos', 'recursos'])
            ->where('ativo', true)
            ->orderByDesc('created_at')
            ->get();
    }

    public function adminIndex()
    {
        return Plano::with(['periodos', 'recursos'])
            ->orderByDesc('created_at')
            ->get();
    }

    public function store(StorePlanRequest $request) // Usando FormRequest
    {
        $plano = DB::transaction(function () use ($request) {
            // Cria o plano com dados validados
            $plano = Plano::create($request->validated());

            // Sincroniza recursos (IDs simples)
            if ($request->has('recursos')) {
                $plano->recursos()->sync($request->recursos);
            }

            // Sincroniza períodos (Array associativo para pivot)
            if ($request->has('periodos')) {
                $periodos = collect($request->periodos)->mapWithKeys(function ($item) {
                    return [$item['id'] => [
                        'valor_centavos' => $item['valor_centavos'],
                        'percentual_desconto' => $item['percentual_desconto'] ?? 0
                    ]];
                })->toArray();

                $plano->periodos()->sync($periodos);
            }

            return $plano;
        });

        return response()->json([
            'message' => 'Plano criado com sucesso!', 
            'id' => $plano->id
        ], 201);
    }

    public function show(Plano $plano)
    {
        return $plano->load(['periodos', 'recursos']);
    }

    public function update(UpdatePlanRequest $request, $plan)
{

    $planModel = $plan instanceof Plano ? $plan : Plano::findOrFail($plan);

    DB::transaction(function () use ($request, $planModel) {
       
        $planModel->update($request->validated());

    
        if ($request->has('recursos')) {
           
            $planModel->recursos()->sync($request->recursos);
        }

        
        if ($request->has('periodos')) {
            $periodos = collect($request->periodos)->mapWithKeys(function ($item) {
                return [$item['id'] => [
                    'valor_centavos' => $item['valor_centavos'],
                    'percentual_desconto' => $item['percentual_desconto'] ?? 0
                ]];
            })->toArray();

            $planModel->periodos()->sync($periodos);
        }
    });

    return response()->json(['message' => 'Plano atualizado com sucesso!']);
}

    public function destroy(Plano $plano)
    {
        // Importante: use $plano->id (PK padrão do Laravel)
        $activeCount = Plano::where('ativo', true)->count();

        if ($plano->ativo && $activeCount < 3) { // Se o plano deletado for ativo, precisa sobrar 2
            return response()->json([
                'message' => 'É necessário manter pelo menos 2 planos ativos.'
            ], 422);
        }

        // Verificação de vínculos usando a PK correta
        $hasUsuarios = Usuario::where('plano_id', $plano->id)->exists();
        $hasAssinaturas = Assinatura::where('plano_id', $plano->id)->exists();

        if ($hasUsuarios || $hasAssinaturas) {
            return response()->json([
                'message' => 'Este plano não pode ser excluído pois possui usuários ou assinaturas vinculadas.'
            ], 422);
        }

        $plano->delete();

        return response()->json(['message' => 'Plano excluído com sucesso.'], 200);
    }
}