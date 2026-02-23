<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        RateLimiter::for('api-global-ip', function (Request $request) {
            $maxAttempts = (int) env('API_RATE_LIMIT_PER_MINUTE', 120);

            return Limit::perMinute(max(1, $maxAttempts))
                ->by($request->ip())
                ->response(function () {
                    return response()->json([
                        'message' => 'Muitas requisicoes deste IP. Tente novamente em instantes.',
                    ], 429);
                });
        });
    }
}
