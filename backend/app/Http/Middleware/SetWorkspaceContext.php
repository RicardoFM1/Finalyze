<?php

namespace App\Http\Middleware;

use App\Models\AccountShare;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetWorkspaceContext
{
    public function handle(Request $request, Closure $next): Response
    {
        $workspaceId = $request->header('X-Workspace-Id');
        $user = $request->user();

        if ($workspaceId && $user) {
            // Check if user has access
            $hasAccess = AccountShare::where('owner_id', $workspaceId)
                ->where('guest_email', $user->email)
                ->exists();

            if ($hasAccess || $user->id == $workspaceId) {
                // Set custom attribute on request
                $request->merge(['workspace_id' => $workspaceId]);

                // Also set in a singleton for easy access in services
                app()->instance('workspace_id', (int)$workspaceId);
            } else {
                return response()->json(['message' => 'Você não tem acesso a esta área de trabalho.'], 403);
            }
        } else if ($user) {
            app()->instance('workspace_id', $user->id);
        }

        return $next($request);
    }
}
