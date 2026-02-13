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
        if (!$user || (!$user->admin && !$user->plano_id)) {
            return response()->json(['message' => 'Forbidden: Plan required.'], 403);
        }

        return $next($request);
    }
}
