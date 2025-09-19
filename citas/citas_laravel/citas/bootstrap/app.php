<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\RoleMiddleware; // 👈 importa tu middleware

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Middleware globales si necesitas
        // $middleware->append(SomeGlobalMiddleware::class);

        // Registrar middleware alias
        $middleware->alias([
            'role' => RoleMiddleware::class, // 👈 aquí lo registras
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
