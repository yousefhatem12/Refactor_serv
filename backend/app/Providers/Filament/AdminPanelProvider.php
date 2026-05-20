<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => \Filament\Support\Colors\Color::hex('#FF8A00'),
                'danger' => \Filament\Support\Colors\Color::Rose,
                'gray' => \Filament\Support\Colors\Color::Zinc,
                'info' => \Filament\Support\Colors\Color::Blue,
                'success' => \Filament\Support\Colors\Color::Emerald,
                'warning' => \Filament\Support\Colors\Color::Orange,
            ])
            ->brandName('Serv5')
            ->brandLogo(asset('logo.svg'))
            ->brandLogoHeight('2.5rem')
            ->font('Cairo')
            ->sidebarCollapsibleOnDesktop()
            ->spa()
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s')
            ->defaultThemeMode(\Filament\Enums\ThemeMode::Dark)
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn (): string => Blade::render('@include("filament.hooks.notification-sound")')
            )
            ->plugins([
                \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make(),
            ])
            ->navigationGroups([
                'Home Page',
                'About Us',
                'Header',
                'Footer',
                'Services',
                'Portfolio',
                'Testimonials',
                'Pages',
                'Blog',
                'SEO & Settings',
                'Media',
                'Our Team',
                'Filament Shield',
                'System & Security',
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
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
