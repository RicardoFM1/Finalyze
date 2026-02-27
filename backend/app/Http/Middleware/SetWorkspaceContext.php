<?php

namespace App\Http\Middleware;

use App\Models\Colaboracao;
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
            
            $hasAccess = Colaboracao::where('proprietario_id', $workspaceId)
                ->where('email_convidado', $user->email)
                ->exists();

            if ($hasAccess || $user->id == $workspaceId) {
                
                $request->merge(['workspace_id' => $workspaceId]);

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
