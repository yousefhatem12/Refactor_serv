<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    private function withAuthCookie(\Illuminate\Http\JsonResponse $response, string $token): \Illuminate\Http\JsonResponse
    {
        $domain = config('sanctum.cookie.domain');
        if (!$domain && app()->environment('production')) {
            $domain = '.serv5.com.eg';
        }

        $secureSetting = config('sanctum.cookie.secure');
        $secure = $secureSetting === null || $secureSetting === ''
            ? app()->environment('production')
            : filter_var($secureSetting, FILTER_VALIDATE_BOOL);

        return $response->withCookie(cookie(
            'auth_token',
            $token,
            (int) config('sanctum.cookie.minutes', 60 * 24 * 30),
            '/',
            $domain,
            $secure,
            true,
            false,
            config('sanctum.cookie.same_site', 'lax')
        ));
    }

    private function authResponse(User $user, string $token)
    {
        $payload = ['user' => $user];

        // Keep bearer-token responses opt-in for legacy API clients. The website uses the HttpOnly cookie.
        // In production, token exposure is always disabled regardless of AUTH_EXPOSE_TOKENS.
        if (! app()->environment('production') && config('sanctum.expose_tokens', false)) {
            $payload['access_token'] = $token;
            $payload['token_type'] = 'Bearer';
        }

        return $this->withAuthCookie(response()->json($payload), $token);
    }

    private function passwordRule(): Password
    {
        $rule = Password::min(8)->mixedCase()->numbers()->symbols();

        return app()->environment('production') ? $rule->uncompromised() : $rule;
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'string', 'confirmed', $this->passwordRule()],
        ], [
            'email.unique' => 'تعذر إتمام التسجيل بهذه البيانات. يرجى التحقق من البريد الإلكتروني أو تسجيل الدخول.',
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
            'password'         => ['required', 'string', 'confirmed', 'different:current_password', $this->passwordRule()],
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
