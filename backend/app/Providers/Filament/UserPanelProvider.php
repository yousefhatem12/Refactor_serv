<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class UserPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('user')
            ->path('user')
            ->login()
            ->registration(\App\Filament\Pages\Auth\RegisterWithOtp::class)
            ->colors([
                'primary' => Color::Amber,
            ])
            ->brandName('Serv5')
            ->brandLogo(asset('logo.svg'))
            ->brandLogoHeight('2.5rem')
            ->font('Cairo')
            ->sidebarCollapsibleOnDesktop()
            ->spa()
            ->defaultThemeMode(\Filament\Enums\ThemeMode::Dark)
            ->renderHook(
                \Filament\View\PanelsRenderHook::HEAD_END,
                fn (): string => '
                    <style>
                        form:not(.fi-modal-window *):not(.fi-modal *) .fi-form-actions, 
                        form:not(.fi-modal-window *):not(.fi-modal *) > .fi-ac,
                        form:not(.fi-modal-window *):not(.fi-modal *) > div > .fi-ac {
                            position: sticky !important;
                            bottom: 1rem !important;
                            z-index: 10 !important;
                            display: flex !important;
                            justify-content: center !important;
                            align-items: center !important;
                            gap: 1rem !important;
                            background-color: rgba(255, 255, 255, 0.45) !important;
                            backdrop-filter: blur(16px) !important;
                            -webkit-backdrop-filter: blur(16px) !important;
                            padding: 0.75rem 1.25rem !important;
                            margin-top: 2rem !important;
                            border: 1px solid rgba(0, 0, 0, 0.05) !important;
                            border-radius: 0.75rem !important;
                            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
                            transition: all 0.3s ease-in-out !important;
                        }

                        form:not(.fi-modal-window *):not(.fi-modal *) .fi-form-actions > div,
                        form:not(.fi-modal-window *):not(.fi-modal *) .fi-ac > div {
                            justify-content: center !important;
                            width: auto !important;
                            flex-grow: 0 !important;
                        }

                        .dark form:not(.fi-modal-window *):not(.fi-modal *) .fi-form-actions, 
                        .dark form:not(.fi-modal-window *):not(.fi-modal *) > .fi-ac,
                        .dark form:not(.fi-modal-window *):not(.fi-modal *) > div > .fi-ac {
                            background-color: rgba(24, 24, 27, 0.45) !important;
                            border: 1px solid rgba(255, 255, 255, 0.1) !important;
                            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.5), 0 4px 6px -4px rgba(0, 0, 0, 0.5) !important;
                        }
                    </style>
                '
            )
            ->plugins([
                \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make(),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/User/Widgets'), for: 'App\Filament\User\Widgets')
            ->widgets([
                AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
