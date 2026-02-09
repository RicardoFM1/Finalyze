<?php

namespace App\Http\Controllers;

use App\Servicos\Assinaturas\ObterDadosAssinatura;
use App\Servicos\Assinaturas\AlternarRenovacaoAutomatica;
use App\Servicos\Assinaturas\CancelarAssinatura;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index(ObterDadosAssinatura $servico)
    {
        return response()->json($servico->executar());
    }

    public function ativarAutoRenovacao(Request $request, AlternarRenovacaoAutomatica $servico)
    {
        try {
            $servico->executar((bool)$request->active);
            return response()->json(['message' => 'Configuração de renovação atualizada']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ?: 404);
        }
    }

    public function cancelar(CancelarAssinatura $servico)
    {
        try {
            $servico->executar();
            return response()->json(['message' => 'Assinatura cancelada. Você terá acesso até o fim do período atual.']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode() ?: 404);
        }
    }
}
