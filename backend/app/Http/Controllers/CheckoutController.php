<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
            // O SDK v3 já desativa SSL internamente quando setRuntimeEnviroment(LOCAL) é chamado
        }
    }

    public function createPreference(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.title' => 'required|string',
            'items.*.unit_price' => 'required|numeric',
            'items.*.quantity' => 'required|integer',
        ]);

        try {
            $this->setupMercadoPago();
            $client = new PreferenceClient();
            
            // Cast items to correct types specifically for SDK
            $items = array_map(function($item) {
                return [
                    "title" => $item['title'],
                    "quantity" => (int)$item['quantity'],
                    "unit_price" => (float)$item['unit_price']
                ];
            }, $request->items);

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

    public function processPayment(Request $request)
    {
        try {
            $this->setupMercadoPago();
            $client = new \MercadoPago\Client\Payment\PaymentClient();

            $data = [
                "payment_method_id" => $request->payment_method_id,
                "transaction_amount" => (float)$request->transaction_amount,
                "description" => $request->description,
                "payer" => [
                    "email" => $request->payer['email'],
                    "identification" => [
                        "type" => $request->payer['identification']['type'] ?? null,
                        "number" => $request->payer['identification']['number'] ?? null
                    ]
                ]
            ];

            // Se não for PIX, adiciona token, issuer_id e installments
            if ($request->payment_method_id !== 'pix') {
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
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Payment processing failed: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function handleWebhook(Request $request)
    {
        \Illuminate\Support\Facades\Log::info('Mercado Pago Webhook Received', $request->all());

        if ($request->type === 'payment' && isset($request->data['id'])) {
            $paymentId = $request->data['id'];
            
            try {
                $this->setupMercadoPago();
                $client = new \MercadoPago\Client\Payment\PaymentClient();
                
                $payment = $client->get($paymentId);
                \Illuminate\Support\Facades\Log::info("Payment Update: ID {$paymentId} is now {$payment->status}");
                
                // TODO: Update subscription/transaction in database
                
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Error processing webhook payment: ' . $e->getMessage());
            }
        }

        return response()->json(['status' => 'success'], 200);
    }
}
