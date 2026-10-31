<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Standard Security Headers
        if ($request->is('api/*')) {
            $contentSecurityPolicy = "default-src 'none'; base-uri 'none'; object-src 'none'; frame-ancestors 'none'; form-action 'none'; script-src 'none'; style-src 'none'; img-src 'none'; connect-src 'none'";
        } else {
            // Filament/Livewire evaluates wire-* expressions in the admin panel.
            // Keep this exception limited to the authenticated admin surface and
            // Livewire transport; public/backend pages remain eval-free.
            $isAdminSurface = $request->is('admin', 'admin/*', 'livewire/*', 'livewire-*/*');
            $scriptSources = $isAdminSurface
                ? "script-src 'self' 'unsafe-inline' 'unsafe-eval'"
                : "script-src 'self' 'unsafe-inline'";

            $contentSecurityPolicy = "default-src 'self'; base-uri 'self'; object-src 'none'; frame-ancestors 'self'; form-action 'self'; " .
                $scriptSources . "; style-src 'self' 'unsafe-inline' https://fonts.bunny.net; " .
                "worker-src 'self' blob:; " .
                "img-src 'self' data: blob: https:; font-src 'self' data: https: https://fonts.bunny.net; " .
                "connect-src 'self' https:; frame-src 'self' https:; media-src 'self' https:; upgrade-insecure-requests";
        }

        $response->headers->set(
            'Content-Security-Policy',
            $contentSecurityPolicy
        );
        $response->headers->set('Strict-Transport-Security', 'max-age=63072000; includeSubDomains; preload');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Hide signatures
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');
        
        if (function_exists('header_remove') && ! headers_sent()) {
            header_remove('X-Powered-By');
            header_remove('Server');
        }

        // Public GET API cache headers
        if ($request->isMethod('GET') && $request->is('api/v1/*') && ! $request->is('api/v1/me')) {
            $response->headers->set('Vary', 'Accept-Language');
            if ($response->getStatusCode() === 200 && ! $response->headers->has('Cache-Control')) {
                $response->headers->set('Cache-Control', 'public, max-age=60, s-maxage=300');
            }
        }
        
        return $response;
    }
}
