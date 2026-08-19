<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    private function withAuthCookie($response, string $token)
    {
        $domain = env('AUTH_COOKIE_DOMAIN');
        if (!$domain && app()->environment('production')) {
            $domain = '.serv5.com.eg';
        }

        $secureSetting = env('AUTH_COOKIE_SECURE');
        $secure = $secureSetting === null || $secureSetting === ''
            ? app()->environment('production')
            : filter_var($secureSetting, FILTER_VALIDATE_BOOL);

        return $response->withCookie(cookie(
            'auth_token',
            $token,
            (int) env('AUTH_COOKIE_MINUTES', 60 * 24 * 30),
            '/',
            $domain,
            $secure,
            true,
            false,
            env('AUTH_COOKIE_SAME_SITE', 'lax')
        ));
    }

    private function authResponse(User $user, string $token)
    {
        $payload = ['user' => $user];

        // Keep bearer-token responses opt-in for legacy API clients. The website uses the HttpOnly cookie.
        // In production, token exposure is always disabled regardless of AUTH_EXPOSE_TOKENS.
        if (! app()->environment('production') && filter_var(env('AUTH_EXPOSE_TOKENS', false), FILTER_VALIDATE_BOOL)) {
            $payload['access_token'] = $token;
            $payload['token_type'] = 'Bearer';
        }

        return $this->withAuthCookie(response()->json($payload), $token);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->authResponse($user, $token);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['البيانات المدخلة غير صحيحة.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->authResponse($user, $token);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'تم تسجيل الخروج بنجاح']);
    }

    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password'         => 'required|string|min:8|confirmed',
        ]);

        $user = $request->user();

        if (! Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['كلمة المرور الحالية غير صحيحة.'],
            ]);
        }

        $user->update(['password' => Hash::make($request->password)]);

        // Revoke ALL tokens except the current one — force re-login on other devices.
        $user->tokens()->where('id', '!=', $request->user()->currentAccessToken()->id)->delete();

        return response()->json(['message' => 'تم تغيير كلمة المرور بنجاح.']);
    }
}
