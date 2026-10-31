<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateTokenFromCookie
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->bearerToken() && $token = $request->cookie('auth_token')) {
            if ($this->isAllowedCookieOrigin($request)) {
                $request->headers->set('Authorization', 'Bearer ' . $token);
            }
        }

        return $next($request);
    }

    private function isAllowedCookieOrigin(Request $request): bool
    {
        $originHeader = $request->headers->get('Origin') ?: $request->headers->get('Referer');

        if ($originHeader) {
            $parsed = parse_url($originHeader);
            if (! is_array($parsed) || empty($parsed['scheme']) || empty($parsed['host'])) {
                return false;
            }

            $origin = $parsed['scheme'] . '://' . $parsed['host']
                . (isset($parsed['port']) ? ':' . $parsed['port'] : '');

            $allowed = array_map(
                fn ($item) => rtrim((string) $item, '/'),
                (array) config('cors.allowed_origins', [])
            );
            $allowed[] = rtrim($request->getSchemeAndHttpHost(), '/');
            $allowed[] = rtrim((string) config('app.url'), '/');

            if (! in_array($origin, $allowed, true)) {
                return false;
            }
        }

        if (! $request->isMethodSafe() && $originHeader) {
            $xRequestedWith = strtolower((string) $request->headers->get('X-Requested-With', ''));
            if ($xRequestedWith !== 'xmlhttprequest' && ! $request->expectsJson() && ! $request->isJson()) {
                return false;
            }
        }

        return true;
    }
}
