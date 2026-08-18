<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ClearAuthCookie
{
    public function handle(Request $request, Closure $next): Response
    {
        $domain = env('AUTH_COOKIE_DOMAIN');
        if (!$domain && app()->environment('production')) {
            $domain = '.serv5.com.eg';
        }

        $secureSetting = env('AUTH_COOKIE_SECURE');
        $secure = $secureSetting === null || $secureSetting === ''
            ? app()->environment('production')
            : filter_var($secureSetting, FILTER_VALIDATE_BOOL);

        return $next($request)->withCookie(cookie(
            'auth_token',
            '',
            -1,
            '/',
            $domain,
            $secure,
            true,
            false,
            env('AUTH_COOKIE_SAME_SITE', 'lax')
        ));
    }
}
