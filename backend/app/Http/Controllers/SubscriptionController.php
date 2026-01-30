<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $subscription = \App\Models\Subscription::where('user_id', $user->id)
            ->where('status', '!=', 'pending')
            ->latest()
            ->first();

        $history = \App\Models\Billing::where('user_id', $user->id)
            ->latest()
            ->get();

        return response()->json([
            'subscription' => $subscription,
            'history' => $history
        ]);
    }

    public function toggleAutoRenew(Request $request)
    {
        $user = auth()->user();
        $subscription = \App\Models\Subscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->first();

        if (!$subscription) {
            return response()->json(['error' => 'Nenhuma assinatura ativa encontrada'], 404);
        }

        $subscription->update([
            'auto_renew' => !(bool)$subscription->auto_renew
        ]);

        return response()->json(['message' => 'Renovação automática atualizada', 'auto_renew' => $subscription->auto_renew]);
    }

    public function cancel(Request $request)
    {
        $user = auth()->user();
        $subscription = \App\Models\Subscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->first();

        if (!$subscription) {
            return response()->json(['error' => 'Nenhuma assinatura ativa encontrada'], 404);
        }

        $subscription->update([
            'status' => 'cancelled',
            'auto_renew' => false
        ]);

        return response()->json(['message' => 'Assinatura cancelada. Você terá acesso até o fim do período atual.']);
    }
}
