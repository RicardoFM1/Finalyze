<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePreferenceRequest;
use App\Http\Requests\ProcessPaymentRequest;
use App\Http\Requests\HandleWebhookRequest;
use App\Models\Assinatura;
use App\Models\Plano;
use App\Models\Periodo;
use App\Models\Usuario;
use App\Models\Faturamento;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;

use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    private function setupMercadoPago()
    {
        $token = config('services.mercadopago.token');

        if (!$token) {
            throw new \Exception('Mercado Pago Token missing');
        }

        MercadoPagoConfig::setAccessToken($token);
        MercadoPagoConfig::setRuntimeEnviroment(MercadoPagoConfig::LOCAL);

        if (config('app.env') === 'local') {
        }
    }

    public function criarPreferencia(CreatePreferenceRequest $request)
    {
        try {
            $this->setupMercadoPago();
            $client = new PreferenceClient();

            $planoId = $request->input('plano_id');
            $periodoId = $request->input('periodo_id');

            $plano = Plano::findOrFail($planoId);
            $periodo = $plano->periodos()->where('periodos.id', $periodoId)->firstOrFail();

            $items = [[
                "title" => $plano->nome . " (" . $periodo->nome . ")",
                "quantity" => 1,
                "unit_price" => $periodo->pivot->valor_centavos / 100
            ]];

            $baseUrl = 'https://roentgenographic-unsensuous-shizue.ngrok-free.dev';

            error_log("DEBUG PAGAMENTO - Base URL: " . $baseUrl);
            \Illuminate\Support\Facades\Log::info('Creating Mercado Pago Preference', [
                'baseUrl' => $baseUrl,
                'items' => $items
            ]);

            $preference = $client->create([
                "items" => $items,
                "back_urls" => [
                    "success" => $baseUrl . "/?status=success",
                    "failure" => $baseUrl . "/?status=failure",
                    "pending" => $baseUrl . "/?status=pending"
                ],
                "auto_return" => "approved",
                "notification_url" => $baseUrl . "/api/webhook/mercadopago"
            ]);

            Assinatura::updateOrCreate(
                ['user_id' => auth()->id(), 'status' => 'pending'],
                [
                    'plano_id' => $plano->id,
                    'periodo_id' => $periodo->id,
                    'mercado_pago_id' => $preference->id,
                ]
            );

            return response()->json(['id' => $preference->id]);
        } catch (\MercadoPago\Exceptions\MPApiException $e) {
            \Illuminate\Support\Facades\Log::error('Mercado Pago API Error', [
                'message' => $e->getMessage(),
                'status_code' => $e->getApiResponse()?->getStatusCode(),
                'response' => $e->getApiResponse()?->getContent(),
            ]);
            return response()->json([
                'error' => 'Api error. Check response for details',
                'details' => $e->getApiResponse()?->getContent()
            ], 500);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Mercado Pago Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function pegarUltimaPreferencia()
    {
        try {
            $assinatura = Assinatura::where('user_id', auth()->id())
                ->where('status', 'pending')
                ->latest()
                ->firstOrFail();

            $plano = Plano::with(['recursos', 'periodos'])->findOrFail($assinatura->plano_id);
            $periodo = $plano->periodos()->where('periodos.id', $assinatura->periodo_id)->first();

            return response()->json([
                'id' => $assinatura->mercado_pago_id,
                'plano' => $plano,
                'periodo_id' => $assinatura->periodo_id,
                'valor_centavos' => $periodo ? $periodo->pivot->valor_centavos : 0
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Nenhuma preferência pendente encontrada'], 404);
        }
    }

    public function processarPagamento(ProcessPaymentRequest $request)
    {
       
        $usuario = auth()->user();
        if (!$usuario) {
            return response()->json(['error' => 'Usuário não autenticado'], 401);
        }

        \Illuminate\Support\Facades\Log::info('ProcessarPagamento Request', [
            'user_id' => $usuario->id,
            'request' => $request->all()
        ]);

        try {
            $this->setupMercadoPago();
            $client = new \MercadoPago\Client\Payment\PaymentClient();

            $transactionAmount = (float) $request->transaction_amount;
            if (!$transactionAmount || $transactionAmount <= 0) {
                return response()->json(['error' => 'transaction_amount inválido'], 422);
            }

            $paymentMethodId = $request->payment_method_id;
            if (!$paymentMethodId) {
                return response()->json(['error' => 'payment_method_id é obrigatório'], 422);
            }

            $plano_id = $request->input('plano_id');
            $periodo_id = $request->input('periodo_id');

            $plano = Plano::findOrFail($plano_id);
            $periodo = $plano->periodos()->where('periodos.id', $periodo_id)->firstOrFail();
            $transactionAmount = $periodo->pivot->valor_centavos / 100;

            $existingPending = Assinatura::where('user_id', $usuario->id)
                ->where('status', 'pending')
                ->where('plano_id', (int)$plano_id)
                ->first();

            if ($existingPending) {
                $assinatura = $existingPending;
            } else {
                $assinatura = Assinatura::create([
                    'user_id' => $usuario->id,
                    'plano_id' => (int)$plano_id,
                    'periodo_id' => (int)$periodo_id,
                    'status' => 'pending',
                    'inicia_em' => now(),
                ]);
            }

            $payer = $request->payer ?? [];
            $payerIdNumber = $payer['identification']['number'] ?? $usuario->cpf;
            if (!$payerIdNumber) {
                if (config('app.env') !== 'production') {
                    $payerIdNumber = '19119119100';
                    $payer['identification']['type'] = 'CPF';
                } else {
                    return response()->json(['error' => 'Número de identificação do pagador é obrigatório'], 422);
                }
            }

            $data = [
                "transaction_amount" => $transactionAmount,
                "payment_method_id" => $paymentMethodId,
                "description" => ($request->description ?? "Assinatura") . " - " . $plano->nome . " (" . $periodo->nome . ")",
                "payer" => [
                    "email" => $usuario->email,
                    "first_name" => explode(' ', $usuario->nome)[0] ?? 'Usuario',
                    "last_name" => implode(' ', array_slice(explode(' ', $usuario->nome), 1)) ?? 'Usuario',
                    "identification" => [
                        "type" => $payer['identification']['type'] ?? 'CPF',
                        "number" => $payerIdNumber
                    ]
                ],
                "external_reference" => (string)$assinatura->id,
                "metadata" => [
                    "user_id" => (string)$usuario->id,
                    "plano_id" => (string)$plano_id,
                    "periodo_id" => (string)$periodo_id,
                    "assinatura_id" => (string)$assinatura->id,
                    "quantidade_dias" => (string)$periodo->quantidade_dias
                ]
            ];

            if ($paymentMethodId === 'pix') {
                $data["date_of_expiration"] = now()->addMinutes(10)->format('Y-m-d\TH:i:s.000O');
            }
            if ($paymentMethodId !== 'pix') {
                if (empty($request->token) || empty($request->issuer_id) || empty($request->installments)) {
                    return response()->json(['error' => 'Token, issuer_id e installments são obrigatórios para cartão'], 422);
                }
                $data["token"] = $request->token;
                $data["issuer_id"] = (int)$request->issuer_id;
                $data["installments"] = (int)$request->installments;
            }

            $payment = $client->create($data);

            return response()->json([
                'status' => $payment->status,
                'status_detail' => $payment->status_detail,
                'id' => $payment->id,
                'qr_code' => $payment->point_of_interaction?->transaction_data?->qr_code,
                'qr_code_base64' => $payment->point_of_interaction?->transaction_data?->qr_code_base64,
                'ticket_url' => $payment->point_of_interaction?->transaction_data?->ticket_url ?? $payment->transaction_details?->external_resource_url,
                'plan_name' => $plano->nome,
                'period_name' => $periodo->nome
            ]);
        } catch (\MercadoPago\Exceptions\MPApiException $e) {
            \Illuminate\Support\Facades\Log::error('MPApiException', [
                'message' => $e->getMessage(),
                'content' => $e->getApiResponse()?->getContent()
            ]);
            return response()->json(['error' => 'Erro na API Mercado Pago', 'details' => $e->getApiResponse()?->getContent()], 422);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('ProcessarPagamento Exception', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Falha ao processar pagamento: ' . $e->getMessage()], 500);
        }
    }

    public function checarStatusPagamento($id)
    {
        try {
            $this->setupMercadoPago();
            $client = new \MercadoPago\Client\Payment\PaymentClient();
            $payment = $client->get($id);

            if ($payment->status === 'approved') {
                $this->_updateUserPlan($payment);
            }

            return response()->json([
                'status' => $payment->status,
                'status_detail' => $payment->status_detail
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Payment not found'], 404);
        }
    }
    public function cancelarAssinatura(Request $request)
    {
        $usuario = auth()->user();
        if (!$usuario) {
            return response()->json(['error' => 'Usuário não autenticado'], 401);
        }

        $assinatura = Assinatura::where('user_id', $usuario->id)
            ->where('status', 'pending')
            ->latest()
            ->first();

        if ($assinatura) {
            $assinatura->update(['status' => 'cancelled']);
            return response()->json(['message' => 'Assinatura cancelada com sucesso']);
        }

        return response()->json(['error' => 'Nenhuma assinatura pendente encontrada'], 404);
    }

    public function handleWebhook(HandleWebhookRequest $request)
    {
        \Illuminate\Support\Facades\Log::info('Mercado Pago Webhook Received', $request->all());

        if ($request->type === 'payment' && isset($request->data['id'])) {
            $paymentId = $request->data['id'];

            try {
                $this->setupMercadoPago();
                $client = new \MercadoPago\Client\Payment\PaymentClient();

                $payment = $client->get($paymentId);
                \Illuminate\Support\Facades\Log::info("Payment Update (Webhook): ID {$paymentId} is now {$payment->status}");

                if ($payment->status === 'approved') {
                    $this->_updateUserPlan($payment);
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Error processing webhook payment: ' . $e->getMessage());
            }
        }

        return response()->json(['status' => 'success'], 200);
    }

    private function _updateUserPlan($payment)
    {
        $metadata = (object)$payment->metadata;

        \Illuminate\Support\Facades\Log::info("Plan Activation Tracking: Processing Payment {$payment->id}", [
            'metadata' => (array)$metadata,
            'external_reference' => $payment->external_reference
        ]);

        $usuarioId = $metadata->user_id ?? null;
        $planoId = $metadata->plano_id ?? null;
        $assinaturaId = $metadata->assinatura_id ?? $payment->external_reference;

        if ($assinaturaId) {
            $assinatura = Assinatura::find($assinaturaId);
            if ($assinatura) {
                $quantidadeDias = $metadata->quantidade_dias ?? 30;
                $newEndsAt = now()->addDays((int)$quantidadeDias);

                $activeSub = Assinatura::where('user_id', $assinatura->user_id)
                    ->where('status', 'active')
                    ->where('id', '!=', $assinatura->id)
                    ->first();

                if ($activeSub && $activeSub->termina_em && $activeSub->termina_em->isFuture()) {
                    $newEndsAt = $activeSub->termina_em->addDays((int)$quantidadeDias);
                    $activeSub->update(['status' => 'cancelled']);
                }

                $assinatura->update([
                    'mercado_pago_id' => $payment->id,
                    'status' => 'active',
                    'inicia_em' => now(),
                    'termina_em' => $newEndsAt
                ]);

                Faturamento::create([
                    'user_id' => $assinatura->user_id,
                    'assinatura_id' => $assinatura->id,
                    'valor_centavos' => (int)($payment->transaction_amount * 100),
                    'status' => 'paid',
                    'metodo_pagamento' => $payment->payment_method_id,
                    'mercado_pago_id' => (string)$payment->id,
                    'pago_em' => now()
                ]);

                \Illuminate\Support\Facades\Log::info("Assinatura {$assinaturaId} activated. Ends at: " . $newEndsAt);

                $usuarioId = $assinatura->user_id;
                $planoId = $assinatura->plano_id;
            }
        }

        if ($usuarioId && $planoId) {
            Usuario::where('id', (int)$usuarioId)
                ->update([
                    'plano_id' => (int)$planoId,
                    'updated_at' => now()
                ]);
        }
    }
}
