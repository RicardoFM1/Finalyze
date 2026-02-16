<?php

namespace App\Servicos\Checkout;

use App\Models\Assinatura;
use App\Models\Periodo;
use App\Models\Plano;
use App\Models\Usuario;
use Illuminate\Support\Facades\Log;
use MercadoPago\Client\Common\RequestOptions;
use MercadoPago\Client\Customer\CustomerClient;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\Client\PreApproval\PreApprovalClient;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Net\MPSearchRequest;

class SubscriptionService
{
    public function __construct()
    {
        $token = config('services.mercadopago.token');
        if (!$token) {
            throw new \Exception('Mercado Pago Token missing');
        }
        MercadoPagoConfig::setAccessToken($token);
        MercadoPagoConfig::setRuntimeEnviroment(MercadoPagoConfig::LOCAL);
    }

    public function createSubscription(Usuario $usuario, Plano $plano, Periodo $periodo, string $cardToken)
    {
        try {
            // 1. Get or Create Customer
            $customerId = $this->getOrCreateCustomer($usuario);

            // 2. Create Preapproval (Subscription)
            $preApprovalClient = new PreApprovalClient();

            $frequency = $this->mapPeriodToFrequency($periodo);

            $data = [
                "payer_email" => $usuario->email,
                "back_url" => config('app.url'),
                "reason" => $plano->nome . " - " . $periodo->nome,
                "external_reference" => "SUB-" . $usuario->id . "-" . time(),
                "auto_recurring" => [
                    "frequency" => $frequency['value'],
                    "frequency_type" => $frequency['type'],
                    "transaction_amount" => (float)number_format(($periodo->pivot->valor_centavos / 100), 2, '.', ''),
                    "currency_id" => "BRL"
                ],
                "card_token_id" => $cardToken,
                "payer_id" => $customerId,
                "status" => "authorized"
            ];

            Log::info("Subscription Data Payload:", $data);

            $subscription = $preApprovalClient->create($data);

            Log::info("Subscription Created: " . ($subscription->id ?? 'no-id'));

            return $subscription;
        } catch (\MercadoPago\Exceptions\MPApiException $e) {
            Log::error("MP Sub API Error: " . $e->getMessage(), [
                'content' => $e->getApiResponse()?->getContent()
            ]);
            throw $e;
        } catch (\Exception $e) {
            Log::error("Subscription Error: " . $e->getMessage());
            throw $e;
        }
    }

    private function getOrCreateCustomer(Usuario $usuario)
    {
        if ($usuario->mercado_pago_customer_id) {
            return $usuario->mercado_pago_customer_id;
        }

        $customerClient = new CustomerClient();

        $searchRequest = new MPSearchRequest(1, 0, ["email" => $usuario->email]);
        $search = $customerClient->search($searchRequest);

        if ($search && count($search->results) > 0) {
            $customer = $search->results[0];
            $usuario->update(['mercado_pago_customer_id' => $customer->id]);
            return $customer->id;
        }

        $customer = $customerClient->create([
            "email" => $usuario->email,
            "first_name" => explode(' ', $usuario->nome)[0],
            "last_name" => implode(' ', array_slice(explode(' ', $usuario->nome), 1)) ?: 'User',
            "identification" => [
                "type" => "CPF",
                "number" => str_replace(['.', '-'], '', $usuario->cpf ?? '19119119100')
            ]
        ]);

        $usuario->update(['mercado_pago_customer_id' => $customer->id]);
        return $customer->id;
    }

    private function mapPeriodToFrequency(Periodo $periodo)
    {
        switch ($periodo->quantidade_dias) {
            case 7:
                return ['value' => 7, 'type' => 'days'];
            case 30:
                return ['value' => 1, 'type' => 'months'];
            case 90:
                return ['value' => 3, 'type' => 'months'];
            case 365:
                return ['value' => 12, 'type' => 'months'];
            default:
                throw new \Exception("Período inválido para assinatura automática: " . $periodo->quantidade_dias . " dias");
        }
    }

    public function toggleAutoRenewal(string $preapprovalId, bool $enable)
    {
        $client = new PreApprovalClient();
        $status = $enable ? 'authorized' : 'paused';
        $client->update($preapprovalId, ['status' => $status]);
        return $status;
    }

    public function cancelSubscription(string $preapprovalId)
    {
        $client = new PreApprovalClient();
        $client->update($preapprovalId, ['status' => 'cancelled']);
        return 'cancelled';
    }
}
