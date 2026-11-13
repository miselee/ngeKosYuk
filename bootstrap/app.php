<?php

use App\Http\Middleware\EnsureUserRole;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => EnsureUserRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        // Redirect expired CSRF forms back gracefully instead of showing a 419 error page.
        $exceptions->respond(function (Response $response, \Throwable $exception, Request $request) {
            if ($response->getStatusCode() === 419 && ! $request->expectsJson()) {
                if ($request->is('logout')) {
                    return redirect('/login')
                        ->with('success', 'Sesi Anda telah berakhir, silakan masuk kembali.');
                }

                if ($request->hasSession()) {
                    $request->session()->regenerateToken();
                }

                return redirect()->back()
                    ->withInput($request->except('password', 'password_confirmation', '_token'))
                    ->with('error', 'Sesi keamanan halaman telah diperbarui. Silakan kirim ulang formulir.');
            }

            return $response;
        });
    })->create();
