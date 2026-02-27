<?php

namespace App\Http\Controllers;

use App\Models\Assinatura;
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

    /**
     * Retorna assinatura e historico de pagamentos do usuario.
     */
    public function index(ObterDadosAssinatura $servico)
    {
        try {
            return response()->json($servico->executar());
        } catch (\Exception $e) {
            Log::error('Erro ao buscar assinaturas: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['error' => 'Falha ao buscar dados de assinatura.'], 500);
        }
    }

    /**
     * Alterna renovacao automatica da assinatura.
     */
    public function ativarAutoRenovacao(Request $request)
    {
        try {
            /** @var \App\Models\Usuario $user */
            $user = auth()->user();

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

            Log::info("Auto-renovacao alterada pelo usuario #{$user->id}: " . ($newState ? 'ON' : 'OFF'));

            return response()->json([
                'message' => 'Renovacao automatica alterada com sucesso.',
                'active' => $newState,
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao alterar renovacao automatica: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Cancela assinatura ativa.
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
                'renovacao_automatica' => false,
            ]);

            return response()->json(['message' => 'Assinatura cancelada com sucesso.']);
        } catch (\Exception $e) {
            Log::error('Erro ao cancelar assinatura: ' . $e->getMessage());
            return response()->json(['error' => 'Falha ao cancelar assinatura.'], 500);
        }
    }

    /**
     * Inicia um periodo de teste de 1 dia.
     */
    public function startTrial(Request $request)
    {
        try {
            /** @var \App\Models\Usuario $user */
            $user = auth()->user();

            if ($user->trial_used_at) {
                return response()->json(['error' => 'Você já utilizou seu período de teste grátis.'], 422);
            }

            if ($user->plano_id || $user->assinaturas()->where('status', 'active')->exists()) {
                return response()->json(['error' => 'Você já possui um plano ativo e não pode iniciar um período de teste.'], 422);
            }

            $request->validate([
                'plano_id' => 'required|exists:planos,id'
            ]);

            $plano = \App\Models\Plano::findOrFail($request->plano_id);

            // Trial de 1 dia
            $assinatura = Assinatura::create([
                'user_id' => $user->id,
                'plano_id' => $plano->id,
                'status' => 'active',
                'inicia_em' => now(),
                'termina_em' => now()->addDay(),
                'renovacao_automatica' => false
            ]);

            $user->update([
                'plano_id' => $plano->id,
                'trial_used_at' => now()
            ]);

            return response()->json([
                'message' => 'Período de teste iniciado! Aproveite por 24 horas.',
                'termina_em' => $assinatura->termina_em
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao iniciar trial: ' . $e->getMessage());
            return response()->json(['error' => 'Falha ao iniciar período de teste.'], 500);
        }
    }
}
