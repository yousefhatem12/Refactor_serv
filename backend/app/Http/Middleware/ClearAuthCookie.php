<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ClearAuthCookie
{
    public function handle(Request $request, Closure $next): Response
    {
        $domain = config('sanctum.cookie.domain');
        if (!$domain && app()->environment('production')) {
            $domain = '.serv5.com.eg';
        }

        $secureSetting = config('sanctum.cookie.secure');
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
            config('sanctum.cookie.same_site', 'lax')
        ));
    }
}
