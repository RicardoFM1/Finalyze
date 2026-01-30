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

        return Plan::where('is_active', true)->orderBy('created_at', 'desc')->get();
    }

    public function adminIndex()
    {
        return Plan::orderBy('created_at', 'desc')->get();
    }

    public function store(StorePlanRequest $request)
    {
        $validated = $request->validated();


        return Plan::create($validated);
    }

    public function show(Plan $plan)
    {
        return $plan;
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
        return $plan;
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
