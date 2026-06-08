<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Register as BaseRegister;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use Filament\Auth\Events\Registered;
use Filament\Facades\Filament;
use Illuminate\Auth\Events\Registered as LaravelRegistered;
use Illuminate\Validation\ValidationException;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Notifications\Notification;
use Filament\Auth\Http\Responses\Contracts\RegistrationResponse;

class RegisterWithOtp extends BaseRegister
{
    public $isOtpStep = false;
    public $otp = '';
    public $tempData = [];

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

            if ($data['otp'] != $this->tempData['otp'] || \Carbon\Carbon::parse($this->tempData['otp_expires_at'])->isPast()) {
                throw ValidationException::withMessages([
                    'data.otp' => 'Invalid or expired OTP code.',
                ]);
            }

            // Create User
            $user = User::create([
                'name' => $this->tempData['name'],
                'email' => $this->tempData['email'],
                'phone' => $this->tempData['phone'],
                'password' => Hash::make($this->tempData['password']),
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
        $this->tempData = $this->form->getState();

        $fullPhone = $this->tempData['country_code'] . ltrim($this->tempData['phone'], '0');

        if (\App\Models\User::where('phone', $fullPhone)->exists()) {
            throw ValidationException::withMessages([
                'data.phone' => 'This phone number is already registered.',
            ]);
        }

        $this->tempData['phone'] = $fullPhone;

        // Generate OTP
        $otpCode = rand(100000, 999999);
        $this->tempData['otp'] = (string) $otpCode;
        $this->tempData['otp_expires_at'] = now()->addMinutes(10);

        // Send WhatsApp
        $this->sendWhatsAppOtp($this->tempData['phone'], $otpCode);

        $this->isOtpStep = true;

        Notification::make()
            ->title('OTP Sent')
            ->body('An OTP code has been sent to your WhatsApp.')
            ->success()
            ->send();

        return null;
    }

    protected function sendWhatsAppOtp($phone, $otp)
    {
        $url = config('services.whatsapp.url');
        $token = config('services.whatsapp.token');
        $sender = config('services.whatsapp.sender');

        if (!$url || !$token) {
            \Log::error('WhatsApp API credentials are missing.');
            return;
        }

        try {
            $response = Http::post($url, [
                'token' => $token,
                'session' => $sender,
                'sender' => $sender,
                'to' => $phone,
                'text' => "رمز التحقق الخاص بك (OTP) للتسجيل في الموقع هو: *{$otp}*\n\nالرمز صالح لمدة 10 دقائق.",
                'messageType' => 'text',
            ]);

            \Log::info('WhatsApp API Response: ' . $response->status() . ' - ' . $response->body());

            if (!$response->successful()) {
                \Log::error('WhatsApp API Error: ' . $response->body());
            }
        } catch (\Exception $e) {
            \Log::error('WhatsApp API Exception: ' . $e->getMessage());
        }
    }
}
