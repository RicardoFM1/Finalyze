<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Plan;

class PlanController extends Controller
{
    public function index(Request $request)
    {
        // If admin (check by token ability or role?), show all. For now simplified:
        // Ideally we check $request->user()?->role === 'admin'
        // But since this is public, we only return active plans unless specifically requested by admin logic (which we don't have usually on public route)
        // Let's assume this is public listing:
        return Plan::where('is_active', true)->orderBy('created_at', 'desc')->get();
    }
    
    public function adminIndex()
    {
        return Plan::orderBy('created_at', 'desc')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric|min:0.01', // No free plans
            'interval' => 'required|string',
            'max_transactions' => 'required|integer',
            'description' => 'nullable|string',
            'features' => 'nullable|array',
            'is_active' => 'boolean'
        ]);

        // Check if we will have less than 2 active plans? No, creating is adding.
        
        return Plan::create($validated);
    }
    
    public function show(Plan $plan)
    {
        return $plan;
    }

    public function update(Request $request, Plan $plan)
    {
        $validated = $request->validate([
            'name' => 'string',
            'price' => 'numeric',
            'interval' => 'string',
            'max_transactions' => 'integer',
            'description' => 'string',
            'features' => 'array'
        ]);

        if (isset($validated['is_active']) && !$validated['is_active']) {
             // If trying to deactivate, check if we have enough active plans
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
        $plan->delete();
        return response()->noContent();
    }
}
