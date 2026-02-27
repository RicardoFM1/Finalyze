<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->header('Accept-Language');

        $user = $request->user();
        if (!$locale && $user && isset($user->idioma)) {
            $locale = $user->idioma;
        }

        if ($locale) {
            if (str_contains($locale, 'pt')) {
                app()->setLocale('pt_BR');
            } else if (str_contains($locale, 'en')) {
                app()->setLocale('en');
            }
        }

        return $next($request);
    }
}
