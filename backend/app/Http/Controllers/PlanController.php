<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StorePlanRequest;
use App\Http\Requests\UpdatePlanRequest;
use App\Models\Plan;
use App\Models\User;
use App\Models\Subscription;

class PlanController extends Controller
{
    public function index(Request $request)
    {

        return Plan::with(['periods', 'features'])->where('is_active', true)->orderBy('created_at', 'desc')->get();
    }

    public function adminIndex()
    {
        return Plan::with(['periods', 'features'])->orderBy('created_at', 'desc')->get();
    }

    public function store(StorePlanRequest $request)
    {
        $validated = $request->validated();
        $plan = Plan::create($validated);

        if ($request->has('features')) {
            $plan->features()->sync($request->features);
        }

        if ($request->has('periods')) {
            foreach ($request->periods as $periodData) {
                $plan->periods()->attach($periodData['id'], [
                    'price_cents' => $periodData['price_cents'],
                    'discount_percentage' => $periodData['discount_percentage'] ?? 0
                ]);
            }
        }

        return $plan->load(['periods', 'features']);
    }

    public function show(Plan $plan)
    {
        return $plan->load(['periods', 'features']);
    }

    public function update(UpdatePlanRequest $request, Plan $plan)
    {
        $validated = $request->validated();

        if (isset($validated['is_active']) && !$validated['is_active']) {
            $activeCount = Plan::where('is_active', true)->where('id', '!=', $plan->id)->count();
            if ($activeCount < 2) {
                return response()->json(['message' => 'É necessário manter pelo menos 2 planos ativos.'], 422);
            }
        }

        $plan->update($validated);

        if ($request->has('features')) {
            $plan->features()->sync($request->features);
        }

        if ($request->has('periods')) {
            $syncData = [];
            foreach ($request->periods as $periodData) {
                $syncData[$periodData['id']] = [
                    'price_cents' => $periodData['price_cents'],
                    'discount_percentage' => $periodData['discount_percentage'] ?? 0
                ];
            }
            $plan->periods()->sync($syncData);
        }

        return $plan->load(['periods', 'features']);
    }

    public function destroy(Plan $plan)
    {
        $activeCount = Plan::where('is_active', true)->where('id', '!=', $plan->id)->count();
        if ($activeCount < 2) {
            return response()->json(['message' => 'É necessário manter pelo menos 2 planos ativos.'], 422);
        }

        $hasUsers = User::where('plan_id', $plan->id)->exists();
        $hasSubscriptions = Subscription::where('plan_id', $plan->id)->exists();

        if ($hasUsers || $hasSubscriptions) {
            return response()->json([
                'message' => 'Este plano não pode ser excluído pois possui usuários ou assinaturas vinculadas.'
            ], 422);
        }

        $plan->delete();
        return response()->noContent();
    }
}
