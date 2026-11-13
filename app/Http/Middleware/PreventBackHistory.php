<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PreventBackHistory
{
    private const VALID_ROLES = ['admin', 'pemilik', 'penyewa'];

    /**
     * Validate active session integrity and prevent browser caching of authenticated pages.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = $request->user();

            if (! $user || ! in_array($user->role, self::VALID_ROLES, true)) {
                Auth::guard('web')->logout();

                if ($request->hasSession()) {
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                }

                $redirect = redirect()->route('login')
                    ->with('error', 'Sesi Anda tidak valid. Silakan masuk kembali.');

                return $this->applySecurityHeaders($redirect);
            }
        }

        $response = $next($request);

        return $this->applySecurityHeaders($response);
    }

    private function applySecurityHeaders(Response $response): Response
    {
        // Prevent HTTP cache and browser bfcache from serving stale views after logout.
        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0, post-check=0, pre-check=0');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        return $response;
    }
}

