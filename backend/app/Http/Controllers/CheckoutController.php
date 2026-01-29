<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePreferenceRequest;
use App\Http\Requests\ProcessPaymentRequest;
use App\Http\Requests\HandleWebhookRequest;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;

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

    public function createPreference(CreatePreferenceRequest $request)
    {


        try {
            $this->setupMercadoPago();
            $client = new PreferenceClient();


            $planId = $request->input('plan_id');
            $plan = \App\Models\Plan::findOrFail($planId);
            $items = [[
                "title" => $plan->name,
                "quantity" => 1,
                "unit_price" => $plan->price_cents / 100
            ]];

            // FORCING NGORK URL FOR TESTING
            $baseUrl = 'https://elina-unrabbinical-consuelo.ngrok-free.dev';

            error_log("DEBUG PAGAMENTO - Base URL: " . $baseUrl);
            \Illuminate\Support\Facades\Log::info('Creating Mercado Pago Preference', [
                'baseUrl' => $baseUrl,
                'items' => $items
            ]);

            $preference = $client->create([
                "items" => $items,
                "back_urls" => [
                    "success" => $baseUrl . "/painel?status=success",
                    "failure" => $baseUrl . "/pagamento?status=failure",
                    "pending" => $baseUrl . "/pagamento?status=pending"
                ],
                "auto_return" => "approved",
                "notification_url" => $baseUrl . "/api/webhook/mercadopago"
            ]);

            // Persistir no Banco de Dados
            \App\Models\Subscription::updateOrCreate(
                ['user_id' => auth()->id(), 'status' => 'pending'],
                [
                    'plan_id' => $plan->id,
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

    public function getLatestPreference()
    {
        try {
            $subscription = \App\Models\Subscription::where('user_id', auth()->id())
                ->where('status', 'pending')
                ->latest()
                ->firstOrFail();

            // Buscar o plano
            $plan = \App\Models\Plan::findOrFail($subscription->plan_id);

            return response()->json([
                'id' => $subscription->mercado_pago_id,
                'plan' => $plan
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Nenhuma preferência pendente encontrada'], 404);
        }
    }

    public function processPayment(ProcessPaymentRequest $request)
{
    $user = auth()->user();
    if (!$user) {
        return response()->json(['error' => 'Usuário não autenticado'], 401);
    }

    \Illuminate\Support\Facades\Log::info('ProcessPayment Request', [
        'user_id' => $user->id,
        'request' => $request->all()
    ]);

    try {
        $this->setupMercadoPago();
        $client = new \MercadoPago\Client\Payment\PaymentClient();

        // Verifica se todos os campos obrigatórios estão presentes
        $transactionAmount = (float) $request->transaction_amount;
        if (!$transactionAmount || $transactionAmount <= 0) {
            return response()->json(['error' => 'transaction_amount inválido'], 422);
        }

        $paymentMethodId = $request->payment_method_id;
        if (!$paymentMethodId) {
            return response()->json(['error' => 'payment_method_id é obrigatório'], 422);
        }

        $payer = $request->payer ?? [];
        $payerIdNumber = $payer['identification']['number'] ?? null;
        if (!$payerIdNumber) {
            return response()->json(['error' => 'Número de identificação do pagador é obrigatório'], 422);
        }

        $data = [
            "transaction_amount" => $transactionAmount,
            "payment_method_id" => $paymentMethodId,
            "description" => $request->description ?? "Assinatura",
            "payer" => [
                "email" => $user->email,
                "first_name" => explode(' ', $user->name)[0] ?? 'User',
                "last_name" => implode(' ', array_slice(explode(' ', $user->name), 1)) ?? 'User',
                "identification" => [
                    "type" => $payer['identification']['type'] ?? 'CPF',
                    "number" => $payerIdNumber
                ]
            ],
            "metadata" => [
                "user_id" => $user->id
            ]
        ];

        // Campos obrigatórios para cartão de crédito
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
            'id' => $payment->id
        ]);
    } catch (\MercadoPago\Exceptions\MPApiException $e) {
        \Illuminate\Support\Facades\Log::error('MPApiException', [
            'message' => $e->getMessage(),
            'content' => $e->getApiResponse()?->getContent()
        ]);
        return response()->json(['error' => 'Erro na API Mercado Pago', 'details' => $e->getApiResponse()?->getContent()], 422);
    } catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::error('ProcessPayment Exception', ['message' => $e->getMessage()]);
        return response()->json(['error' => 'Falha ao processar pagamento: '.$e->getMessage()], 500);
    }
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
                \Illuminate\Support\Facades\Log::info("Payment Update: ID {$paymentId} is now {$payment->status}");

                

            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Error processing webhook payment: ' . $e->getMessage());
            }
        }

        return response()->json(['status' => 'success'], 200);
    }
}
