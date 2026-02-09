<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckResource
{
    public function handle(Request $request, Closure $next, string $resourceSlug): Response
    {
        $usuario = $request->user();

        if ($usuario && $usuario->admin) {
            return $next($request);
        }

        if (!$usuario || !$usuario->plano) {
            return response()->json(['message' => 'Você precisa de um plano ativo para acessar este recurso.'], 403);
        }

        $hasResource = $usuario->plano->recursos()->where('slug', $resourceSlug)->exists();

        if (!$hasResource) {
            return response()->json(['message' => "Seu plano não inclui o recurso: {$resourceSlug}."], 403);
        }

        return $next($request);
    }
}
