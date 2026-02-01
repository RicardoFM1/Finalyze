<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StorePlanRequest;
use App\Http\Requests\UpdatePlanRequest;
use App\Models\Plano;
use App\Models\Usuario;
use App\Models\Assinatura;

class PlanController extends Controller
{
    public function index(Request $request)
    {
        return Plano::with(['periodos', 'recursos'])->where('ativo', true)->orderBy('created_at', 'desc')->get();
    }

    public function adminIndex()
    {
        return Plano::with(['periodos', 'recursos'])->orderBy('created_at', 'desc')->get();
    }

    public function store(StorePlanRequest $request)
    {
        $validated = $request->validated();
        $plano = Plano::create($validated);

        if ($request->has('recursos')) {
            $plano->recursos()->sync($request->recursos);
        }

        if ($request->has('periodos')) {
            foreach ($request->periodos as $periodoData) {
                $plano->periodos()->attach($periodoData['id'], [
                    'valor_centavos' => $periodoData['valor_centavos'],
                    'percentual_desconto' => $periodoData['percentual_desconto'] ?? 0
                ]);
            }
        }

        return $plano->load(['periodos', 'recursos']);
    }

    public function show(Plano $plano)
    {
        return $plano->load(['periodos', 'recursos']);
    }

    public function update(UpdatePlanRequest $request, Plano $plano)
    {
        $validated = $request->validated();

        if (isset($validated['ativo']) && !$validated['ativo']) {
            $activeCount = Plano::where('ativo', true)->where('id', '!=', $plano->id)->count();
            if ($activeCount < 2) {
                return response()->json(['message' => 'É necessário manter pelo menos 2 planos ativos.'], 422);
            }
        }

        $plano->update($validated);

        if ($request->has('recursos')) {
            $plano->recursos()->sync($request->recursos);
        }

        if ($request->has('periodos')) {
            $syncData = [];
            foreach ($request->periodos as $periodoData) {
                $syncData[$periodoData['id']] = [
                    'valor_centavos' => $periodoData['valor_centavos'],
                    'percentual_desconto' => $periodoData['percentual_desconto'] ?? 0
                ];
            }
            $plano->periodos()->sync($syncData);
        }

        return $plano->load(['periodos', 'recursos']);
    }

    public function destroy(Plano $plano)
    {
        $activeCount = Plano::where('ativo', true)->where('id', '!=', $plano->id)->count();
        if ($activeCount < 2) {
            return response()->json(['message' => 'É necessário manter pelo menos 2 planos ativos.'], 422);
        }

        $hasUsuarios = Usuario::where('plano_id', $plano->id)->exists();
        $hasAssinaturas = Assinatura::where('plano_id', $plano->id)->exists();

        if ($hasUsuarios || $hasAssinaturas) {
            return response()->json([
                'message' => 'Este plano não pode ser excluído pois possui usuários ou assinaturas vinculadas.'
            ], 422);
        }

        $plano->delete();
        return response()->noContent();
    }
}
