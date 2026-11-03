<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AiCredentialResource\Pages;
use App\Models\AiCredential;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Placeholder;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class AiCredentialResource extends Resource
{
    protected static ?string $model = AiCredential::class;
    
    public static function getNavigationGroup(): ?string 
    { 
        return __('AI Content Tools'); 
    }
    
    protected static ?int $navigationSort = 1;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-key';

    public static function getNavigationLabel(): string 
    { 
        return __('AI API Credentials'); 
    }

    public static function shouldRegisterNavigation(): bool
    {
        return config('ai.enabled', false);
    }

    public static function getModelLabel(): string
    {
        return __('AI Credential');
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'صلاحيات الـ API' : 'AI Credentials';
    }

    /**
     * Token to query the provider with: the one being typed, else the stored one.
     */
    protected static function resolveFormToken(callable $get, ?AiCredential $record): ?string
    {
        $typed = $get('new_api_token');

        return filled($typed) ? $typed : \App\Services\Ai\ModelCatalog::decrypt($record);
    }

    public static function form(Schema $schema): Schema
    {
        $isAr = app()->getLocale() === 'ar';
        return $schema
            ->components([
                Section::make($isAr ? 'الإعدادات الأساسية' : 'Basic Credentials')
                    ->schema([
                        Select::make('provider')
                            ->label($isAr ? 'مزود الخدمة' : 'Provider')
                            ->options([
                                'gemini' => 'Google Gemini',
                                'openai' => 'OpenAI',
                                'deepseek' => 'DeepSeek',
                            ])
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn ($set) => $set('default_model', null)),

                        TextInput::make('new_api_token')
                            ->label($isAr ? 'مفتاح الـ API' : 'API Token')
                            ->password()
                            // Blur refreshes the model list against the freshly typed key.
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($set) => $set('default_model', null))
                            ->afterStateHydrated(function (TextInput $component, $state) {
                                $component->state(null);
                            })
                            ->placeholder(function ($record) use ($isAr) {
                                if ($record && $record->token_hint) {
                                    return $isAr 
                                        ? "مشفر (ينتهي بـ ...{$record->token_hint})" 
                                        : "Encrypted (ends with ...{$record->token_hint})";
                                }
                                return $isAr ? 'أدخل مفتاح الـ API' : 'Enter API token';
                            }),

                        Select::make('default_model')
                            ->label($isAr ? 'الموديل الافتراضي' : 'Default Model')
                            ->options(fn ($get, $record) => \App\Services\Ai\ModelCatalog::options(
                                $get('provider'),
                                self::resolveFormToken($get, $record)
                            ))
                            ->helperText(function ($get, $record) use ($isAr) {
                                $provider = $get('provider');
                                if (!$provider) {
                                    return null;
                                }

                                $token = self::resolveFormToken($get, $record);
                                if (blank($token)) {
                                    return $isAr
                                        ? 'أدخل مفتاح الـ API لجلب الموديلات المتاحة فعلياً من المزود.'
                                        : 'Enter the API token to load the models this key can actually use.';
                                }

                                $result = \App\Services\Ai\ModelCatalog::fetch($provider, $token);
                                if ($result['source'] === 'live') {
                                    return $isAr
                                        ? 'تم جلب الموديلات مباشرة من المزود.'
                                        : 'Models fetched live from the provider.';
                                }

                                return ($isAr ? 'تعذر جلب الموديلات، عرض القائمة الاحتياطية. ' : 'Could not fetch models, showing the fallback list. ')
                                    . ($result['error'] ?? '');
                            })
                            ->suffixAction(
                                \Filament\Actions\Action::make('refreshModels')
                                    ->label($isAr ? 'تحديث الموديلات' : 'Refresh models')
                                    ->icon('heroicon-m-arrow-path')
                                    ->action(function ($get, $set, $record) {
                                        \App\Services\Ai\ModelCatalog::forget(
                                            $get('provider'),
                                            self::resolveFormToken($get, $record)
                                        );
                                        $set('default_model', $get('default_model'));
                                    })
                            )
                            ->required(),

                        Toggle::make('is_active')
                            ->label($isAr ? 'نشط' : 'Active')
                            ->default(false)
                            ->reactive(),

                        TextEntry::make('connection_status')
                            ->label($isAr ? 'حالة الاتصال' : 'Connection Status')
                            ->state(function ($record) use ($isAr) {
                                if (!$record) {
                                    return $isAr ? 'لم ينشأ بعد' : 'Not Created Yet';
                                }
                                if (!$record->is_active) {
                                    return $isAr ? 'غير نشط' : 'Inactive';
                                }
                                if ($record->last_test_status === 'success') {
                                    return $isAr ? 'متصل' : 'Connected';
                                }
                                if ($record->last_test_status === 'failed') {
                                    return $isAr ? 'فشل الاتصال' : 'Failed';
                                }
                                return $isAr ? 'لم يتم الفحص بعد' : 'Not Tested';
                            }),
                    ]),

                Section::make($isAr ? 'إعدادات متقدمة' : 'Advanced Settings')
                    ->collapsed()
                    ->schema([
                        TextInput::make('default_language')
                            ->label($isAr ? 'اللغة الافتراضية' : 'Default Language')
                            ->placeholder('ar, en')
                            ->default('ar')
                            ->required(),

                        TextInput::make('default_article_length')
                            ->label($isAr ? 'طول المقال الافتراضي (كلمة)' : 'Default Article Length (Words)')
                            ->numeric()
                            ->default(1200)
                            ->required(),

                        TextInput::make('temperature')
                            ->label($isAr ? 'درجة الحرارة (Temperature)' : 'Temperature')
                            ->numeric()
                            ->step(0.1)
                            ->default(0.7)
                            ->required(),

                        TextInput::make('max_articles_per_request')
                            ->label($isAr ? 'أقصى عدد مقالات في الطلب' : 'Max Articles Per Request')
                            ->numeric()
                            ->default(10)
                            ->required(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        $isAr = app()->getLocale() === 'ar';
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('provider')
                    ->label($isAr ? 'المزود' : 'Provider')
                    ->badge()
                    ->formatStateUsing(fn ($state) => strtoupper($state)),

                Tables\Columns\TextColumn::make('token_hint')
                    ->label($isAr ? 'تلميح المفتاح' : 'Token Hint')
                    ->formatStateUsing(fn ($state) => $state ? "...{$state}" : 'None'),

                Tables\Columns\TextColumn::make('default_model')
                    ->label($isAr ? 'الموديل الافتراضي' : 'Default Model'),

                Tables\Columns\TextColumn::make('connection_state')
                    ->label($isAr ? 'حالة الاتصال' : 'Connection Status')
                    ->badge()
                    ->getStateUsing(function ($record) {
                        if (!$record->is_active) {
                            return 'Inactive';
                        }
                        if ($record->last_test_status === 'success') {
                            return 'Connected';
                        }
                        if ($record->last_test_status === 'failed') {
                            return 'Failed';
                        }
                        return 'Not Tested';
                    })
                    ->color(fn ($state) => match ($state) {
                        'Connected' => 'success',
                        'Failed' => 'danger',
                        'Inactive' => 'gray',
                        default => 'warning'
                    }),

                Tables\Columns\TextColumn::make('last_tested_at')
                    ->label($isAr ? 'آخر فحص' : 'Last Tested')
                    ->dateTime()
                    ->placeholder('Never'),
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\Action::make('testConnection')
                    ->label($isAr ? 'فحص الاتصال' : 'Test Connection')
                    ->icon('heroicon-o-bolt')
                    ->disabled(function ($record) {
                        // Disable if no token hint/saved api token exists
                        return empty($record->token_hint);
                    })
                    ->action(function (AiCredential $record) use ($isAr) {
                        try {
                            $token = $record->getDecryptedToken();
                            if (empty($token)) {
                                throw new \Exception("No API token set to test connection.");
                            }

                            $provider = \App\Services\Ai\AiProviderManager::getProvider($record->getAttribute('provider'));
                            $success = $provider->testConnection($token);

                            $record->update([
                                'last_tested_at' => now(),
                                'last_test_status' => $success ? 'success' : 'failed',
                                'last_test_error' => $success ? null : 'API Connection check returned failure.',
                            ]);

                            if ($success) {
                                \Filament\Notifications\Notification::make()
                                    ->title($isAr ? 'نجاح الاتصال' : 'Connection Successful')
                                    ->success()
                                    ->send();

                                // A model that the provider retired keeps returning 400 at
                                // generation time, so surface it while the key is being tested.
                                $live = \App\Services\Ai\ModelCatalog::fetch($record->getAttribute('provider'), $token, true);
                                if ($live['source'] === 'live' && $record->getAttribute('default_model') && !array_key_exists($record->getAttribute('default_model'), $live['models'])) {
                                    \Filament\Notifications\Notification::make()
                                        ->title($isAr ? 'الموديل الافتراضي غير مدعوم' : 'Default model no longer supported')
                                        ->body(($isAr ? 'المزود لا يدعم ' : 'The provider no longer offers ') . $record->getAttribute('default_model')
                                            . ($isAr ? '. المتاح: ' : '. Available: ') . implode(', ', array_slice(array_keys($live['models']), 0, 10)))
                                        ->warning()
                                        ->persistent()
                                        ->send();
                                }
                            } else {
                                \Filament\Notifications\Notification::make()
                                    ->title($isAr ? 'فشل الاتصال' : 'Connection Failed')
                                    ->danger()
                                    ->send();
                            }
                        } catch (\Throwable $e) {
                            $record->update([
                                'last_tested_at' => now(),
                                'last_test_status' => 'failed',
                                'last_test_error' => $e->getMessage(),
                            ]);

                            \Filament\Notifications\Notification::make()
                                ->title($isAr ? 'خطأ في الاتصال' : 'Connection Error')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAiCredentials::route('/'),
            'create' => Pages\CreateAiCredential::route('/create'),
            'edit' => Pages\EditAiCredential::route('/{record}/edit'),
        ];
    }
}
