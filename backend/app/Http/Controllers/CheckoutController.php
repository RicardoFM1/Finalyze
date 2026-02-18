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
            $data = $servico->executar($request->all());
            return response()->json($data);
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

            // Extrai dados do QR Code com segurança (pode não existir em cartão)
            $qrCode = $payment->point_of_interaction?->transaction_data?->qr_code ?? null;
            $qrCodeBase64 = $payment->point_of_interaction?->transaction_data?->qr_code_base64 ?? null;
            $ticketUrl = $payment->point_of_interaction?->transaction_data?->ticket_url ?? $payment->transaction_details?->external_resource_url ?? null;

            return response()->json([
                'status' => $payment->status,
                'status_detail' => $payment->status_detail,
                'id' => $payment->id,
                'qr_code' => $qrCode,
                'qr_code_base64' => $qrCodeBase64,
                'ticket_url' => $ticketUrl,
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

    public function checkUpgrade(Request $request)
    {
        $request->validate([
            'plano_id' => 'required|exists:planos,id',
            'periodo_id' => 'required|exists:periodos,id'
        ]);

        $usuario = auth()->user();
        $assinaturaAtiva = $usuario->assinaturaAtiva();

        if (!$assinaturaAtiva || ($assinaturaAtiva->plano_id == $request->plano_id && $assinaturaAtiva->periodo_id == $request->periodo_id)) {
            return response()->json(['creditos' => 0, 'gratuito' => false]);
        }

        $calculadora = new \App\Servicos\Checkout\CalculadoraProrata();
        $creditos = $calculadora->calcularCredito($assinaturaAtiva);

        $plano = \App\Models\Plano::findOrFail($request->plano_id);
        $periodo = $plano->periodos()->findOrFail($request->periodo_id);
        $valorPlano = $periodo->pivot->valor_centavos / 100;

        \Log::info("Upgrade Check:", [
            'user' => $usuario->id,
            'current_sub' => $assinaturaAtiva->id,
            'creditos' => $creditos,
            'valor_plano' => $valorPlano,
            'gratuito' => $creditos >= $valorPlano
        ]);

        return response()->json([
            'creditos' => $creditos,
            'valor_plano' => $valorPlano,
            'valor_final' => max(0, $valorPlano - $creditos),
            'gratuito' => $creditos >= $valorPlano
        ]);
    }

    public function applyFreeUpgrade(Request $request, AtivarPlanoUsuario $servicoAtivacao)
    {
        $request->validate([
            'plano_id' => 'required|exists:planos,id',
            'periodo_id' => 'required|exists:periodos,id'
        ]);

        $usuario = auth()->user();
        $assinaturaAtiva = $usuario->assinaturaAtiva();

        if (!$assinaturaAtiva) {
            return response()->json(['error' => 'Nenhuma assinatura ativa encontrada.'], 422);
        }

        $calculadora = new \App\Servicos\Checkout\CalculadoraProrata();
        $creditos = $calculadora->calcularCredito($assinaturaAtiva);

        $plano = \App\Models\Plano::findOrFail($request->plano_id);
        $periodo = $plano->periodos()->findOrFail($request->periodo_id);
        $valorPlano = $periodo->pivot->valor_centavos / 100;

        if ($creditos < $valorPlano) {
            return response()->json(['error' => 'Saldo insuficiente para upgrade gratuito.'], 422);
        }

        // Criar a assinatura pendente se necessário (ou usar AtivarPlanoSubscriber logic)
        // Para simplificar, vamos criar a assinatura e ativar
        $assinatura = \App\Models\Assinatura::create([
            'user_id' => $usuario->id,
            'plano_id' => $plano->id,
            'periodo_id' => $periodo->id,
            'status' => 'pending',
            'valor_original_centavos' => $periodo->pivot->valor_centavos,
        ]);

        // Mock payment object for AtivarPlanoUsuario
        $mockPayment = (object)[
            'id' => 'FREE-UP-' . time(),
            'transaction_amount' => 0,
            'payment_method_id' => 'prorrata_credit',
            'status' => 'approved',
            'status_detail' => 'accredited',
            'metadata' => [
                'user_id' => $usuario->id,
                'plano_id' => $plano->id,
                'assinatura_id' => $assinatura->id,
                'quantidade_dias' => $periodo->pivot->dias,
                'creditos_prorrata' => $creditos
            ]
        ];

        $servicoAtivacao->executar($mockPayment);

        return response()->json(['message' => 'Upgrade realizado com sucesso!']);
    }
}
