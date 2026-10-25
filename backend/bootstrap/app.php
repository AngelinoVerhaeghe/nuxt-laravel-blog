<?php

use App\Http\Middleware\EnsureEmailIsVerified;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Sanctum session auth for requests from the frontend, and the 'api' rate limiter (AppServiceProvider)
        $middleware->statefulApi();
        $middleware->throttleApi();

        // Where the 'guest' middleware sends users who are already logged in
        $middleware->redirectUsersTo('/dashboard');

        // Answers unverified users with JSON instead of a redirect
        $middleware->alias([
            'verified' => EnsureEmailIsVerified::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
