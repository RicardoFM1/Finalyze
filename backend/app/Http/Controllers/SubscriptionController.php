<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $subscription = \App\Models\Assinatura::where('user_id', $user->id)
            ->with(['plano', 'periodo'])
            ->latest()
            ->first();

        $history = \App\Models\Faturamento::where('user_id', $user->id)
            ->with(['assinatura.plano'])
            ->latest()
            ->get();

        return response()->json([
            'assinatura' => $subscription,
            'historico' => $history
        ]);
    }

    public function ativarAutoRenovacao(Request $request)
    {
        $user = auth()->user();
        $subscription = \App\Models\Assinatura::where('user_id', $user->id)
            ->where('status', 'active')
            ->first();

        if (!$subscription) {
            return response()->json(['message' => 'Nenhuma assinatura ativa encontrada'], 404);
        }

        $subscription->update(['renovacao_automatica' => $request->active]);

        return response()->json(['message' => 'Configuração de renovação atualizada']);
    }

    public function cancelar()
    {
        $user = auth()->user();
        $subscription = \App\Models\Assinatura::where('user_id', $user->id)
            ->where('status', 'active')
            ->first();

        if (!$subscription) {
            return response()->json(['error' => 'Nenhuma assinatura ativa encontrada'], 404);
        }

        $subscription->update([
            'status' => 'cancelled',
            'renovacao_automatica' => false
        ]);

        return response()->json(['message' => 'Assinatura cancelada. Você terá acesso até o fim do período atual.']);
    }
}
