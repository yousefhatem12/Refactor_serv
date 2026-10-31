<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Register as BaseRegister;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Filament\Auth\Events\Registered;
use Filament\Facades\Filament;
use Illuminate\Auth\Events\Registered as LaravelRegistered;
use Illuminate\Validation\ValidationException;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Notifications\Notification;
use Filament\Auth\Http\Responses\Contracts\RegistrationResponse;
use Livewire\Attributes\Locked;

class RegisterWithOtp extends BaseRegister
{
    #[Locked]
    public bool $isOtpStep = false;

    #[Locked]
    public string $regKey = '';

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

        return $schema
            ->components([
                $this->getNameFormComponent(),
                $this->getEmailFormComponent(),
                \Filament\Schemas\Components\Grid::make(3)
                    ->schema([
                        \Filament\Forms\Components\Select::make('country_code')
                            ->label('Country Code')
                            ->options([
                                '20' => 'Egypt (+20)',
                                '966' => 'Saudi Arabia (+966)',
                                '971' => 'UAE (+971)',
                                '965' => 'Kuwait (+965)',
                                '974' => 'Qatar (+974)',
                                '973' => 'Bahrain (+973)',
                                '968' => 'Oman (+968)',
                            ])
                            ->default('20')
                            ->searchable()
                            ->required()
                            ->columnSpan(1),
                        TextInput::make('phone')
                            ->label('Phone Number (WhatsApp)')
                            ->required()
                            ->tel()
                            ->columnSpan(2),
                    ]),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
            ]);
    }

    public function register(): ?RegistrationResponse
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();
            return null;
        }

        if ($this->isOtpStep) {
            $data = $this->form->getState();
            $cached = $this->regKey !== '' ? Cache::get($this->regKey) : null;

            if (! is_array($cached)) {
                $this->isOtpStep = false;
                $this->regKey = '';
                throw ValidationException::withMessages([
                    'data.otp' => 'Invalid or expired OTP code.',
                ]);
            }

            if (! Hash::check((string) ($data['otp'] ?? ''), (string) ($cached['otp_hash'] ?? ''))) {
                $cached['attempts'] = ((int) ($cached['attempts'] ?? 0)) + 1;

                if ($cached['attempts'] >= 5) {
                    Cache::forget($this->regKey);
                    $this->isOtpStep = false;
                    $this->regKey = '';
                } else {
                    Cache::put($this->regKey, $cached, now()->addMinutes(10));
                }

                throw ValidationException::withMessages([
                    'data.otp' => 'Invalid or expired OTP code.',
                ]);
            }

            Cache::forget($this->regKey);
            $this->regKey = '';

            // Create User
            $user = User::create([
                'name' => $cached['name'],
                'email' => $cached['email'],
                'phone' => $cached['phone'],
                'password' => $cached['password_hash'],
                'otp' => null,
                'otp_expires_at' => null,
            ]);

            event(new Registered($user));
            event(new LaravelRegistered($user));

            Filament::auth()->login($user);

            session()->regenerate();

            return app(RegistrationResponse::class);
        }

        // Validate first step
        $formData = $this->form->getState();

        $fullPhone = $formData['country_code'] . ltrim($formData['phone'], '0');

        if (User::where('phone', $fullPhone)->exists()) {
            throw ValidationException::withMessages([
                'data.phone' => 'This phone number is already registered.',
            ]);
        }

        // Generate cryptographically secure OTP
        $otpCode = (string) random_int(100000, 999999);
        $key = 'reg_otp:' . Str::uuid();

        Cache::put($key, [
            'name' => $formData['name'],
            'email' => $formData['email'],
            'phone' => $fullPhone,
            'password_hash' => Hash::make($formData['password']),
            'otp_hash' => Hash::make($otpCode),
            'attempts' => 0,
        ], now()->addMinutes(10));

        $this->regKey = $key;
        $this->isOtpStep = true;
        // Clear sensitive form inputs (password) from Livewire public $data snapshot
        $this->data = ['otp' => ''];

        // Send WhatsApp
        $this->sendWhatsAppOtp($fullPhone, $otpCode);

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
            $response = Http::timeout(10)->post($url, [
                'token' => $token,
                'session' => $sender,
                'sender' => $sender,
                'to' => $phone,
                'text' => "رمز التحقق الخاص بك (OTP) للتسجيل في الموقع هو: *{$otp}*\n\nالرمز صالح لمدة 10 دقائق.",
                'messageType' => 'text',
            ]);

            if (!$response->successful()) {
                Log::error('WhatsApp API Error: ' . $response->status());
            }
        } catch (\Exception $e) {
            Log::error('WhatsApp API Exception: ' . $e->getMessage());
        }
    }
}
