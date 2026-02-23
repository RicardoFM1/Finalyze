<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->appendToGroup('api', 'throttle:api-global-ip');

        $middleware->alias([
            'has_plan' => \App\Http\Middleware\EnsureUserHasPlan::class,
            'admin'    => \App\Http\Middleware\EnsureUserIsAdmin::class,
            'check_resource' => \App\Http\Middleware\CheckResource::class,
            'workspace' => \App\Http\Middleware\SetWorkspaceContext::class,
            'set_locale' => \App\Http\Middleware\SetLocale::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {})->create();
