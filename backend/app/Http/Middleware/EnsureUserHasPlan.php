<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasPlan
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $workspaceId = app()->bound('workspace_id') ? app('workspace_id') : ($user ? $user->id : null);

        $targetUser = $user;
        if ($workspaceId && $user && $user->id != $workspaceId) {
            $targetUser = \App\Models\Usuario::find($workspaceId);
        }

        if (!$targetUser || (!$targetUser->admin && !$targetUser->plano_id)) {
            return response()->json(['message' => 'Esta conta precisa de um plano ativo.'], 403);
        }

        return $next($request);
    }
}
