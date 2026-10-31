<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Component;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Locked;

class LoginWithOtp extends BaseLogin
{
    #[Locked]
    public bool $isOtpStep = false;

    public $otp = '';

    #[Locked]
    public ?int $userId = null;

    public function form(Schema $schema): Schema
    {
        if ($this->isOtpStep) {
            return $schema
                ->components([
                    TextInput::make('otp')
                        ->label('OTP Code')
                        ->placeholder('Enter the 6-digit code sent to WhatsApp')
                        ->required()
                        ->numeric()
                        ->length(6)
                        ->autofocus(),
                ]);
        }

        return parent::form($schema);
    }

    public function authenticate(): ?LoginResponse
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();
            return null;
        }

        $data = $this->form->getState();

        if ($this->isOtpStep) {
            // Validate OTP
            $user = User::find($this->userId);

            if (!$user || !Hash::check($data['otp'], $user->otp) || \Carbon\Carbon::parse($user->otp_expires_at)->isPast()) {
                throw ValidationException::withMessages([
                    'data.otp' => 'Invalid or expired OTP code.',
                ]);
            }

            // Clear OTP
            $user->update([
                'otp' => null,
                'otp_expires_at' => null,
            ]);

            // Login
            Filament::auth()->login($user, $this->data['remember'] ?? false);
            
            session()->regenerate();

            return app(LoginResponse::class);
        }

        // Step 1: Validate Email & Password
        $data = $this->form->getState();

        if (! Filament::auth()->attempt([
            'email' => $data['email'],
            'password' => $data['password'],
        ])) {
            $this->throwFailureValidationException();
        }

        /** @var \App\Models\User $user */
        $user = Filament::auth()->user();
        Filament::auth()->logout(); // Don't log in yet

        // Check if user has a phone number
        if (!$user->phone) {
            Notification::make()
                ->title('No Phone Number')
                ->body('Your account does not have a phone number. Please contact the administrator.')
                ->danger()
                ->send();
            return null;
        }

        // Generate OTP
        $otpCode = random_int(100000, 999999);
        $user->update([
            'otp' => Hash::make($otpCode),
            'otp_expires_at' => now()->addMinutes(10),
        ]);

        // Send OTP via WhatsApp API
        $this->sendWhatsAppOtp($user->phone, $otpCode);

        // Switch to OTP step
        $this->isOtpStep = true;
        $this->userId = $user->id;

        Notification::make()
            ->title('OTP Sent')
            ->body('An OTP code has been sent to your WhatsApp.')
            ->success()
            ->send();

        return null;
    }

    protected function sendWhatsAppOtp(string $phone, string $otp)
    {
        $url = config('services.whatsapp.url');
        $token = config('services.whatsapp.token');
        $sender = config('services.whatsapp.sender');

        if (!$url || !$token) {
            Log::error('WhatsApp API credentials are missing.');
            return;
        }

        try {
            $response = Http::post($url, [
                'token' => $token,
                'session' => $sender,
                'sender' => $sender,
                'to' => $phone,
                'text' => "رمز التحقق الخاص بك (OTP) للدخول إلى لوحة التحكم هو: *{$otp}*\n\nالرمز صالح لمدة 10 دقائق.",
                'messageType' => 'text',
            ]);

            if (!$response->successful()) {
                Log::error('WhatsApp API Error: ' . $response->body());
            }
        } catch (\Exception $e) {
            Log::error('WhatsApp API Exception: ' . $e->getMessage());
        }
    }
}
