<?php

namespace App\Servicos\Checkout;

use App\Models\Assinatura;
use App\Models\Plano;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;
use Illuminate\Support\Facades\Log;

class CriarPreferenciaCheckout
{
    private function setupMercadoPago()
    {
        $token = config('services.mercadopago.token');
        if (!$token) {
            throw new \Exception('Mercado Pago Token missing');
        }
        MercadoPagoConfig::setAccessToken($token);
        MercadoPagoConfig::setRuntimeEnviroment(MercadoPagoConfig::SERVER);
    }

    public function executar(array $dados)
    {
        $this->setupMercadoPago();
        $client = new PreferenceClient();

        $planoId = $dados['plano_id'];
        $periodoId = $dados['periodo_id'];

        $plano = Plano::findOrFail($planoId);
        $periodo = $plano->periodos()->where('periodos.id', $periodoId)->firstOrFail();

        // Cálculo de Prorrata
        /** @var \App\Models\Usuario $usuario */
        $usuario = auth()->user();
        $assinaturaAtiva = $usuario->assinaturaAtiva();
        $creditos = 0;
        $totalPagar = $periodo->pivot->valor_centavos / 100;

        if ($assinaturaAtiva && ($assinaturaAtiva->plano_id != $plano->id || $assinaturaAtiva->periodo_id != $periodo->id)) {
            $calculadora = new CalculadoraProrata();
            $creditos = $calculadora->calcularCredito($assinaturaAtiva);
            $totalPagar = max(0, $totalPagar - $creditos);
        }

        $items = [[
            "title" => $plano->nome . " (" . $periodo->nome . ")",
            "quantity" => 1,
            "unit_price" => (float)$totalPagar
        ]];

        $baseUrl = config('app.url');

        Log::info('Creating Mercado Pago Preference', [
            'baseUrl' => $baseUrl,
            'items' => $items,
            'user_id' => $usuario->id,
            'creditos_aplicados' => $creditos
        ]);

        // Criamos uma assinatura pendente primeiro para ter o ID
        $assinatura = Assinatura::create([
            'user_id' => $usuario->id,
            'plano_id' => $plano->id,
            'periodo_id' => $periodo->id,
            'status' => 'pending'
        ]);

        $preferenceData = [
            "items" => $items,
            "external_reference" => (string)$assinatura->id,
            "metadata" => [
                "user_id" => $usuario->id,
                "plano_id" => $plano->id,
                "assinatura_id" => $assinatura->id,
                "quantidade_dias" => $periodo->quantidade_dias,
                "creditos_prorrata" => $creditos
            ],
            "back_urls" => [
                "success" => $baseUrl . "/?status=success",
                "failure" => $baseUrl . "/?status=failure",
                "pending" => $baseUrl . "/?status=pending"
            ],
            "auto_return" => "approved",
            "notification_url" => $baseUrl . "/api/webhook/mercadopago"
        ];

        $preference = $client->create($preferenceData);

        // Atualizamos a assinatura com o ID da preferência
        $assinatura->update(['mercado_pago_id' => $preference->id]);

        // Cancelamos outras assinaturas pendentes para evitar duplicidade
        Assinatura::where('user_id', $usuario->id)
            ->where('status', 'pending')
            ->where('id', '!=', $assinatura->id)
            ->update(['status' => 'cancelled']);

        return $preference->id;
    }
}
