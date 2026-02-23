<?php

namespace App\Http\Controllers;

use App\Models\Assinatura;
use App\Models\Faturamento;
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

    /**
     * Retorna a assinatura ativa do usuário e o histórico de pagamentos.
     * Chamado por Profile.vue -> fetchSubscription
     */
    public function index()
    {
        try {
            $user = auth()->user();

            $assinatura = Assinatura::where('user_id', $user->id)
                ->with(['plano', 'periodo'])
                ->orderByRaw("CASE
                    WHEN status = 'active' THEN 1
                    WHEN status = 'authorized' THEN 2
                    WHEN status = 'pending' THEN 3
                    ELSE 4 END")
                ->orderBy('termina_em', 'desc')
                ->first();

            $historico = Faturamento::where('user_id', $user->id)
                ->with(['assinatura.plano', 'assinatura.periodo'])
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'assinatura' => $assinatura,
                'historico' => $historico
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar assinaturas: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Falha ao buscar dados de assinatura.'], 500);
        }
    }

    /**
     * Alterna a renovação automática (Ligar/Desligar).
     * Chamado por Profile.vue -> ativarAutoRenovacao
     */
    public function ativarAutoRenovacao(Request $request)
    {
        try {
            /** @var \App\Models\Usuario $user */
            $user = auth()->user();
            $assinatura = Assinatura::where('user_id', $user->id)
                ->where('status', 'active')
                ->whereNotNull('preapproval_id')
                ->first();

            if (!$assinatura) {
                return response()->json(['message' => 'Nenhuma assinatura ativa com renovação automática encontrada.'], 404);
            }

            $currentState = (bool) $assinatura->renovacao_automatica;
            $newState = $request->has('active') ? (bool) $request->input('active') : !$currentState;

            $status = $this->subscriptionService->toggleAutoRenewal($assinatura->preapproval_id, $newState);

            $assinatura->update(['renovacao_automatica' => $newState]);

            return response()->json([
                'message' => 'Renovação automática ' . ($newState ? 'ativada' : 'desativada') . ' com sucesso.',
                'active' => $newState,
                'status' => $status
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao alterar renovação automática: ' . $e->getMessage());
            return response()->json(['error' => 'Falha ao processar solicitação.'], 500);
        }
    }

    /**
     * Cancela a assinatura (status = cancelled).
     * Chamado por ModalCancelarAssinatura -> confirmCancel
     */
    public function cancelar()
    {
        try {
            /** @var \App\Models\Usuario $user */
            $user = auth()->user();
            $assinatura = Assinatura::where('user_id', $user->id)
                ->where('status', 'active')
                ->first();

            if (!$assinatura) {
                return response()->json(['message' => 'Nenhuma assinatura ativa encontrada.'], 404);
            }

            if ($assinatura->preapproval_id) {
                $this->subscriptionService->cancelSubscription($assinatura->preapproval_id);
            }

            $assinatura->update([
                'status' => 'cancelled',
                'renovacao_automatica' => false
            ]);

            return response()->json(['message' => 'Assinatura cancelada com sucesso.']);
        } catch (\Exception $e) {
            Log::error('Erro ao cancelar assinatura: ' . $e->getMessage());
            return response()->json(['error' => 'Falha ao cancelar assinatura.'], 500);
        }
    }
}
