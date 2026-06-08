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
        $response->headers->set('Content-Security-Policy', "default-src 'self' http: https: data: blob: 'unsafe-inline' 'unsafe-eval'");
        $response->headers->set('Strict-Transport-Security', 'max-age=63072000; includeSubDomains; preload');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Hide signatures
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');
        
        if (function_exists('header_remove')) {
            header_remove('X-Powered-By');
            header_remove('Server');
        }
        
        return $response;
    }
}
