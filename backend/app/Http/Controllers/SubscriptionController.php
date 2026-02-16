<?php

namespace App\Http\Controllers;

use App\Models\Assinatura;
use App\Servicos\Checkout\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SubscriptionController extends Controller
{
    protected $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    public function toggleAutoRenewal(Request $request)
    {
        try {
            $user = auth()->user();
            $assinatura = Assinatura::where('user_id', $user->id)
                ->where('status', 'active')
                ->whereNotNull('preapproval_id')
                ->first();

            if (!$assinatura) {
                return response()->json(['message' => 'Nenhuma assinatura ativa com renovação automática encontrada.'], 404);
            }

            $enable = $request->input('enable'); // true or false

            $status = $this->subscriptionService->toggleAutoRenewal($assinatura->preapproval_id, $enable);

            $assinatura->update(['auto_renew' => $enable]);

            return response()->json([
                'message' => 'Renovação automática ' . ($enable ? 'ativada' : 'desativada') . ' com sucesso.',
                'status' => $status,
                'auto_renew' => $enable
            ]);
        } catch (\Exception $e) {
            Log::error("Erro ao alterar renovação automática: " . $e->getMessage());
            return response()->json(['error' => 'Falha ao processar solicitação.'], 500);
        }
    }
}
