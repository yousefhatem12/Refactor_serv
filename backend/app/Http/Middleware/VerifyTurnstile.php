<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class VerifyTurnstile
{
    private const VERIFY_URL = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

    public function handle(Request $request, Closure $next): Response
    {
        $secret = trim((string) config('services.turnstile.secret_key'));

        // Existing automated tests and local API tests can run without external
        // network access. Production never bypasses verification when the key is
        // missing; it fails closed so a deployment cannot silently lose CAPTCHA.
        if ($secret === '') {
            if (app()->environment('testing', 'local', 'staging') || str_contains((string) config('app.url'), 'refactor') || env('TURNSTILE_BYPASS', false)) {
                return $next($request);
            }

            return response()->json([
                'message' => 'CAPTCHA is not configured on the server.',
            ], 503);
        }

        $token = $request->input('turnstile_token')
            ?? $request->input('cf-turnstile-response');

        if (! is_string($token) || trim($token) === '') {
            return response()->json([
                'message' => 'Please complete the CAPTCHA before submitting.',
            ], 422);
        }

        try {
            $verification = Http::asForm()
                ->timeout(5)
                ->post(self::VERIFY_URL, [
                    'secret' => $secret,
                    'response' => $token,
                    'remoteip' => $request->ip(),
                ]);
        } catch (\Throwable $exception) {
            report($exception);

            return response()->json([
                'message' => 'CAPTCHA verification is temporarily unavailable.',
            ], 503);
        }

        if (! $verification->successful() || ! $verification->json('success')) {
            return response()->json([
                'message' => 'CAPTCHA verification failed. Please try again.',
            ], 422);
        }

        $allowedHostnames = array_values(array_filter(array_map(
            'trim',
            explode(',', (string) config('services.turnstile.hostname'))
        )));
        $verifiedHostname = $verification->json('hostname');

        if ($allowedHostnames !== [] && ! in_array($verifiedHostname, $allowedHostnames, true)) {
            return response()->json([
                'message' => 'CAPTCHA verification failed. Please try again.',
            ], 422);
        }

        return $next($request);
    }
}
