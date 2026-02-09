<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePreferenceRequest;
use App\Http\Requests\ProcessPaymentRequest;
use App\Http\Requests\HandleWebhookRequest;
use App\Servicos\Checkout\CriarPreferenciaCheckout;
use App\Servicos\Checkout\ObterUltimaPreferenciaCheckout;
use App\Servicos\Checkout\ProcessarPagamentoCheckout;
use App\Servicos\Checkout\ChecarStatusPagamentoCheckout;
use App\Servicos\Checkout\AtivarPlanoUsuario;
use App\Servicos\Checkout\CancelarPagamentoCheckout;
use App\Servicos\Checkout\ProcessarWebhookCheckout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    public function criarPreferencia(CreatePreferenceRequest $request, CriarPreferenciaCheckout $servico)
    {
        try {
            $id = $servico->executar($request->all());
            return response()->json(['id' => $id]);
        } catch (\MercadoPago\Exceptions\MPApiException $e) {
            Log::error('Mercado Pago API Error', [
                'message' => $e->getMessage(),
                'status_code' => $e->getApiResponse()?->getStatusCode(),
                'response' => $e->getApiResponse()?->getContent(),
            ]);
            return response()->json([
                'error' => 'Api error. Check response for details',
                'details' => $e->getApiResponse()?->getContent()
            ], 500);
        } catch (\Exception $e) {
            Log::error('Mercado Pago Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function pegarUltimaPreferencia(ObterUltimaPreferenciaCheckout $servico)
    {
        try {
            return response()->json($servico->executar());
        } catch (\Exception $e) {
            return response()->json(['error' => 'Nenhuma preferência pendente encontrada'], 404);
        }
    }

    public function processarPagamento(ProcessPaymentRequest $request, ProcessarPagamentoCheckout $servico)
    {
        try {
            $payment = $servico->executar($request->all());

            return response()->json([
                'status' => $payment->status,
                'status_detail' => $payment->status_detail,
                'id' => $payment->id,
                'qr_code' => $payment->point_of_interaction?->transaction_data?->qr_code,
                'qr_code_base64' => $payment->point_of_interaction?->transaction_data?->qr_code_base64,
                'ticket_url' => $payment->point_of_interaction?->transaction_data?->ticket_url ?? $payment->transaction_details?->external_resource_url,
            ]);
        } catch (\MercadoPago\Exceptions\MPApiException $e) {
            Log::error('MPApiException', [
                'message' => $e->getMessage(),
                'content' => $e->getApiResponse()?->getContent()
            ]);
            return response()->json(['error' => 'Erro na API Mercado Pago', 'details' => $e->getApiResponse()?->getContent()], 422);
        } catch (\Exception $e) {
            Log::error('ProcessarPagamento Exception', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Falha ao processar pagamento: ' . $e->getMessage()], 500);
        }
    }

    public function checarStatusPagamento($id, ChecarStatusPagamentoCheckout $servico, AtivarPlanoUsuario $ativarPlanoServico)
    {
        try {
            return response()->json($servico->executar($id, $ativarPlanoServico));
        } catch (\Exception $e) {
            return response()->json(['error' => 'Payment not found'], 404);
        }
    }

    public function cancelarPagamento(Request $request, CancelarPagamentoCheckout $servico)
    {
        if ($servico->executar()) {
            return response()->json(['message' => 'Assinatura cancelada com sucesso']);
        }
        return response()->json(['error' => 'Nenhuma assinatura pendente encontrada'], 404);
    }

    public function handleWebhook(HandleWebhookRequest $request, ProcessarWebhookCheckout $servico, AtivarPlanoUsuario $ativarPlanoServico)
    {
        $servico->executar($request->all(), $ativarPlanoServico);
        return response()->json(['status' => 'success'], 200);
    }
}
