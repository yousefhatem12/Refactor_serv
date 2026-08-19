<?php

namespace Tests\Unit;

use App\Http\Middleware\VerifyTurnstile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class VerifyTurnstileTest extends TestCase
{
    #[Test]
    public function it_rejects_a_request_without_a_token(): void
    {
        config(['services.turnstile.secret_key' => 'test-secret']);

        $request = Request::create('/api/v1/contact', 'POST');
        $response = app(VerifyTurnstile::class)->handle(
            $request,
            fn ($request) => response()->json(['ok' => true])
        );

        $this->assertSame(422, $response->getStatusCode());
        $this->assertSame(
            'Please complete the CAPTCHA before submitting.',
            $response->getData(true)['message']
        );
        Http::assertNothingSent();
    }

    #[Test]
    public function it_rejects_an_invalid_token(): void
    {
        config(['services.turnstile.secret_key' => 'test-secret']);
        Http::fake([
            'https://challenges.cloudflare.com/turnstile/v0/siteverify' => Http::response([
                'success' => false,
            ]),
        ]);

        $request = Request::create('/api/v1/contact', 'POST', [
            'turnstile_token' => 'invalid-token',
        ]);
        $response = app(VerifyTurnstile::class)->handle(
            $request,
            fn ($request) => response()->json(['ok' => true])
        );

        $this->assertSame(422, $response->getStatusCode());
        Http::assertSent(fn ($httpRequest) => $httpRequest->data()['response'] === 'invalid-token');
    }

    #[Test]
    public function it_allows_a_valid_token_to_continue(): void
    {
        config([
            'services.turnstile.secret_key' => 'test-secret',
            'services.turnstile.hostname' => '',
        ]);
        Http::fake([
            'https://challenges.cloudflare.com/turnstile/v0/siteverify' => Http::response([
                'success' => true,
            ]),
        ]);

        $request = Request::create('/api/v1/contact', 'POST', [
            'turnstile_token' => 'valid-token',
        ]);
        $response = app(VerifyTurnstile::class)->handle(
            $request,
            fn ($request) => response()->json(['ok' => true])
        );

        $this->assertSame(200, $response->getStatusCode());
        $this->assertTrue($response->getData(true)['ok']);
    }
}
