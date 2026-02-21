<?php

namespace App\Servicos\Checkout;

use App\Models\Plano;
use App\Models\Periodo;
use App\Models\Usuario;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\PreApproval\PreApprovalClient;
use MercadoPago\Client\Customer\CustomerClient;
use Illuminate\Support\Facades\Log;

class SubscriptionService
{
    public function setupMercadoPago()
    {
        $token = config('services.mercadopago.token');
        if (!$token) {
            throw new \Exception('Mercado Pago Token missing');
        }
        MercadoPagoConfig::setAccessToken($token);
        MercadoPagoConfig::setRuntimeEnviroment(MercadoPagoConfig::SERVER);
    }

    public function createSubscription(Usuario $usuario, Plano $plano, Periodo $periodo, string $cardToken, float $creditos = 0, $assinaturaId = null)
    {
        try {
            $this->setupMercadoPago();

            // 1. Get or Create Customer
            $customerId = $this->getOrCreateCustomer($usuario);

            // 2. Create Preapproval (Subscription)
            $preApprovalClient = new PreApprovalClient();

            $frequency = $this->mapPeriodToFrequency($periodo);

            $basePrice = ($periodo->pivot->valor_centavos / 100);

            // Valor com desconto (Prorrata)
            // IMPORTANTE: Se o Mercado Pago Preapproval recebe um transaction_amount, ele cobra esse valor EM TODAS AS RENOVAÇÕES.
            // Para assinaturas recorrentes, o ideal é que o desconto seja apenas no primeiro mês.
            // No entanto, para evitar que o usuário pague o valor cheio no primeiro mês quando deveria ter desconto,
            // vamos aplicar o desconto agora. Se o usuário quiser cobrar valor cheio na renovação, 
            // precisaríamos de um fluxo de upgrade mais complexo (Payment + Subscription futura).
            $transactionAmount = max(0, $basePrice - $creditos);

            // Prepare payer identification
            $cpfNumber = str_replace(['.', '-', ' '], '', $usuario->cpf ?? '');
            if (empty($cpfNumber) && config('app.env') !== 'production') {
                $cpfNumber = '19119119100'; // Test CPF
            }

            $firstName = explode(' ', $usuario->nome)[0] ?? 'Usuario';
            $lastName = implode(' ', array_slice(explode(' ', $usuario->nome), 1)) ?: 'Finalyze';

            $data = [
                "payer_email" => (string)$usuario->email,
                "back_url" => config('app.url'),
                "reason" => $plano->nome . " - " . $periodo->nome,
                "external_reference" => (string)($assinaturaId ?? "SUB-" . $usuario->id . "-" . time()),
                "auto_recurring" => [
                    "frequency" => (int)$frequency['value'],
                    "frequency_type" => (string)$frequency['type'],
                    "transaction_amount" => (float)number_format($transactionAmount, 2, '.', ''),
                    "currency_id" => "BRL"
                ],
                "card_token_id" => (string)$cardToken,
                "status" => "authorized"
            ];

            Log::info("Criando assinatura no Mercado Pago:", [
                'user_id' => $usuario->id,
                'amount' => $transactionAmount,
                'external_ref' => $data['external_reference']
            ]);

            $subscription = $preApprovalClient->create($data);

            return $subscription;
        } catch (\Exception $e) {
            Log::error("Erro no SubscriptionService: " . $e->getMessage());
            throw $e;
        }
    }

    public function toggleAutoRenewal(string $preapprovalId, bool $active)
    {
        try {
            $this->setupMercadoPago();
            $preApprovalClient = new PreApprovalClient();

            $status = $active ? "authorized" : "paused";

            return $preApprovalClient->update($preapprovalId, [
                "status" => $status
            ]);
        } catch (\Exception $e) {
            Log::error("Erro ao alterar renovação automática: " . $e->getMessage());
            throw $e;
        }
    }

    public function cancelSubscription(string $preapprovalId)
    {
        try {
            $this->setupMercadoPago();
            $preApprovalClient = new PreApprovalClient();

            return $preApprovalClient->update($preapprovalId, [
                "status" => "cancelled"
            ]);
        } catch (\Exception $e) {
            Log::error("Erro ao cancelar assinatura no MP: " . $e->getMessage());
            throw $e;
        }
    }

    private function getOrCreateCustomer(Usuario $usuario)
    {
        $this->setupMercadoPago();
        $client = new CustomerClient();

        if ($usuario->mercado_pago_customer_id) {
            try {
                return $client->get($usuario->mercado_pago_customer_id)->id;
            } catch (\Exception $e) {
                // Se não encontrar, segue para criar um novo
            }
        }

        $customer = $client->create([
            "email" => $usuario->email,
            "first_name" => explode(' ', $usuario->nome)[0] ?? 'Usuario',
            "last_name" => implode(' ', array_slice(explode(' ', $usuario->nome), 1)) ?: 'Finalyze'
        ]);

        $usuario->update(['mercado_pago_customer_id' => $customer->id]);

        return $customer->id;
    }

    private function mapPeriodToFrequency(Periodo $periodo)
    {
        $slug = $periodo->slug;

        switch ($slug) {
            case 'semanal':
                return ['value' => 1, 'type' => 'days']; // Ajustar se necessário
            case 'mensal':
                return ['value' => 1, 'type' => 'months'];
            case 'trimestral':
                return ['value' => 3, 'type' => 'months'];
            case 'anual':
                return ['value' => 1, 'type' => 'years'];
            default:
                return ['value' => 1, 'type' => 'months'];
        }
    }
}
