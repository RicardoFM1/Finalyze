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
        $workspaceId = app()->bound('workspace_id') ? app('workspace_id') : ($usuario ? $usuario->id : null);

        $targetUser = $usuario;
        if ($workspaceId && $usuario && $usuario->id != $workspaceId) {
            $targetUser = \App\Models\Usuario::with('plano.recursos')->find($workspaceId);
        }

        if ($targetUser && $targetUser->admin) {
            return $next($request);
        }

        if (!$targetUser || !$targetUser->plano) {
            return response()->json(['message' => 'Esta conta precisa de um plano ativo para acessar este recurso.'], 403);
        }

        $hasResource = $targetUser->plano->recursos()->where('slug', $resourceSlug)->exists();

        if (!$hasResource) {
            return response()->json(['message' => "O plano desta conta não inclui o recurso: {$resourceSlug}."], 403);
        }

        return $next($request);
    }
}
