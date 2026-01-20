<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;

class CheckoutController extends Controller
{
    public function createPreference(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.title' => 'required|string',
            'items.*.unit_price' => 'required|numeric',
            'items.*.quantity' => 'required|integer',
        ]);

        $token = env('MERCADO_PAGO_ACCESS_TOKEN');
        
        if (!$token) {
             \Illuminate\Support\Facades\Log::error('Mercado Pago Token missing');
             return response()->json(['error' => 'Mercado Pago Token not configured'], 500);
        }

        MercadoPagoConfig::setAccessToken($token);
        MercadoPagoConfig::setRuntimeEnviroment(MercadoPagoConfig::LOCAL);

        $client = new PreferenceClient();
        
        try {
            // Cast items to correct types specifically for SDK
            $items = array_map(function($item) {
                return [
                    "title" => $item['title'],
                    "quantity" => (int)$item['quantity'],
                    "unit_price" => (float)$item['unit_price']
                ];
            }, $request->items);

            $preference = $client->create([
                "items" => $items,
                "back_urls" => [
                    "success" => env('APP_URL') . "/painel?status=success",
                    "failure" => env('APP_URL') . "/pagamento?status=failure",
                    "pending" => env('APP_URL') . "/pagamento?status=pending"
                ],
                "auto_return" => "approved",
            ]);

            return response()->json(['id' => $preference->id]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Mercado Pago Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
