<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CorsPreflightTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_allows_cors_preflight_with_accept_language_from_localhost_3000(): void
    {
        $response = $this->call(
            'OPTIONS',
            '/api/v1/testimonials?lang=en',
            [],
            [],
            [],
            [
                'HTTP_ORIGIN' => 'http://localhost:3000',
                'HTTP_ACCESS_CONTROL_REQUEST_METHOD' => 'GET',
                'HTTP_ACCESS_CONTROL_REQUEST_HEADERS' => 'content-type,accept,accept-language',
            ]
        );

        $response->assertStatus(204);
        $this->assertSame('http://localhost:3000', $response->headers->get('Access-Control-Allow-Origin'));
        $this->assertSame('true', $response->headers->get('Access-Control-Allow-Credentials'));

        $allowedHeaders = strtolower((string) $response->headers->get('Access-Control-Allow-Headers'));
        $this->assertStringContainsString('accept-language', $allowedHeaders);
        $this->assertStringContainsString('content-type', $allowedHeaders);
    }

    #[Test]
    public function it_preserves_vary_origin_and_accept_language_on_public_get_requests(): void
    {
        $response = $this->withHeaders([
            'Origin' => 'http://localhost:3000',
            'Accept-Language' => 'en',
        ])->getJson('/api/v1/testimonials?lang=en');

        $response->assertOk();
        $this->assertSame('http://localhost:3000', $response->headers->get('Access-Control-Allow-Origin'));
        $this->assertSame('true', $response->headers->get('Access-Control-Allow-Credentials'));

        $vary = (string) $response->headers->get('Vary');
        $this->assertStringContainsString('Origin', $vary);
        $this->assertStringContainsString('Accept-Language', $vary);

        $altSvc = (string) $response->headers->get('Alt-Svc');
        $this->assertStringContainsString('h3=":443"', $altSvc);
    }
}

