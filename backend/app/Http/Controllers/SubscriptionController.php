<?php

namespace App\Http\Controllers;

use App\Models\Assinatura;
use App\Models\Usuario;
use App\Servicos\Assinaturas\ObterDadosAssinatura;
use App\Servicos\Checkout\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SubscriptionController extends Controller
{
    protected SubscriptionService $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    public function index(Request $request, ObterDadosAssinatura $servico)
    {
        try {
            return response()->json($servico->executar());
        } catch (\Exception $e) {
            /** @var Usuario $user */
            $user = $request->user();
            Log::error('Erro ao buscar assinaturas: ' . $e->getMessage(), [
                'user_id' => $user?->id,
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['error' => 'Falha ao buscar dados de assinatura.'], 500);
        }
    }

    public function ativarAutoRenovacao(Request $request)
    {
        try {
            /** @var Usuario $user */
            $user = $request->user();

            $assinatura = Assinatura::where('user_id', $user->id)
                ->whereIn('status', ['active', 'authorized'])
                ->orderByDesc('termina_em')
                ->first();

            if (!$assinatura) {
                return response()->json(['message' => 'Nenhuma assinatura ativa encontrada.'], 404);
            }

            $oldState = (bool) $assinatura->renovacao_automatica;
            $newState = $request->has('active') ? $request->boolean('active') : !$oldState;

            if ($assinatura->preapproval_id) {
                $this->subscriptionService->setupMercadoPago();
                $this->subscriptionService->toggleAutoRenewal($assinatura->preapproval_id, $newState);
            }

            $assinatura->update(['renovacao_automatica' => $newState]);

            Log::info('Auto-renovacao alterada pelo usuario: ' . $user->id . ', novo estado: ' . ($newState ? 'ativo' : 'inativo'));

            return response()->json([
                'message' => 'Renovacao automatica alterada com sucesso.',
                'active' => $newState,
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao alterar renovacao automatica: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function cancelar(Request $request)
    {
        try {
            /** @var Usuario $user */
            $user = $request->user();

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
                'renovacao_automatica' => false,
            ]);

            return response()->json(['message' => 'Assinatura cancelada com sucesso.']);
        } catch (\Exception $e) {
            Log::error('Erro ao cancelar assinatura: ' . $e->getMessage());
            return response()->json(['error' => 'Falha ao cancelar assinatura.'], 500);
        }
    }
}
