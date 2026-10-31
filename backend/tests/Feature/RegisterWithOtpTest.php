<?php

namespace Tests\Feature;

use App\Filament\Pages\Auth\RegisterWithOtp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RegisterWithOtpTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function registration_otp_and_password_are_never_exposed_in_livewire_snapshot(): void
    {
        config([
            'services.whatsapp.url' => 'https://example.com/whatsapp',
            'services.whatsapp.token' => 'test-token',
            'services.whatsapp.sender' => 'serv5',
        ]);
        Http::fake(['example.com/*' => Http::response(['ok' => true], 200)]);

        $secretPassword = 'SuperSecretPassword!99';

        $component = Livewire::test(RegisterWithOtp::class)
            ->set('data.name', 'Test Customer')
            ->set('data.email', 'customer@example.com')
            ->set('data.country_code', '20')
            ->set('data.phone', '01012345678')
            ->set('data.password', $secretPassword)
            ->set('data.passwordConfirmation', $secretPassword)
            ->call('register')
            ->assertSet('isOtpStep', true);

        $snapshotJson = json_encode($component->snapshot);
        $this->assertStringNotContainsString($secretPassword, $snapshotJson);
        $this->assertStringNotContainsString('tempData', $snapshotJson);

        $regKey = $component->get('regKey');
        $this->assertNotEmpty($regKey);
        $cached = Cache::get($regKey);
        $this->assertIsArray($cached);
        $this->assertArrayHasKey('otp_hash', $cached);
        $this->assertArrayNotHasKey('otp', $cached);
        $this->assertArrayNotHasKey('password', $cached);
    }

    #[Test]
    public function otp_verification_locks_out_after_five_failed_attempts_and_succeeds_with_valid_code(): void
    {
        config([
            'services.whatsapp.url' => 'https://example.com/whatsapp',
            'services.whatsapp.token' => 'test-token',
            'services.whatsapp.sender' => 'serv5',
        ]);

        $capturedOtp = null;
        Http::fake(function ($request) use (&$capturedOtp) {
            if (preg_match('/\*(\d{6})\*/', (string) ($request['text'] ?? ''), $matches)) {
                $capturedOtp = $matches[1];
            }

            return Http::response(['ok' => true], 200);
        });

        $component = Livewire::test(RegisterWithOtp::class)
            ->set('data.name', 'Verified User')
            ->set('data.email', 'verified@example.com')
            ->set('data.country_code', '20')
            ->set('data.phone', '01099998888')
            ->set('data.password', 'ValidPassword!123')
            ->set('data.passwordConfirmation', 'ValidPassword!123')
            ->call('register')
            ->assertSet('isOtpStep', true);

        $this->assertNotNull($capturedOtp);
        $regKey = $component->get('regKey');

        // 3 failed attempts keep cache entry alive with incremented counter
        for ($i = 1; $i <= 3; $i++) {
            $component->set('data.otp', '000000')->call('register');
            $this->assertSame($i, Cache::get($regKey)['attempts']);
        }

        // Valid attempt on 4th try succeeds and creates user
        $component->set('data.otp', $capturedOtp)->call('register');
        $this->assertNull(Cache::get($regKey));
        $this->assertDatabaseHas('users', [
            'email' => 'verified@example.com',
            'phone' => '201099998888',
        ]);
    }
}
