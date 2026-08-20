<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use App\Filament\Pages\Dashboard;
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
                'primary' => Color::hex('#FF8A00'),
                'danger' => Color::Rose,
                'gray' => Color::Zinc,
                'info' => Color::Blue,
                'success' => Color::Emerald,
                'warning' => Color::Orange,
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
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => '
                    <style>
                        /* Allow sticky positioning to stick relative to the viewport by resetting intermediate layout container overflows */
                        .fi-layout,
                        .fi-main-ctn,
                        .fi-main,
                        .fi-page,
                        .fi-page-header-main-ctn,
                        .fi-page-main,
                        .fi-page-content,
                        form {
                            overflow: visible !important;
                        }

                        /* Keep floating actions scoped to authenticated panel pages. */
                        .fi-layout form .fi-form-actions,
                        .fi-layout form .fi-sc-actions .fi-ac,
                        .fi-layout form > .fi-ac {
                            position: fixed !important;
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
                            border: 1px solid rgba(0, 0, 0, 0.05) !important;
                            border-radius: 0.75rem !important;
                            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
                            transition: all 0.3s ease-in-out !important;

                            /* Center horizontally inside the main content area, offsetting the sidebar */
                            left: calc(50% + (var(--sidebar-width, 16rem) / 2)) !important;
                            transform: translateX(-50%) !important;
                        }

                        /* Override fixed offset for RTL layout */
                        html[dir="rtl"] .fi-layout form .fi-form-actions,
                        html[dir="rtl"] .fi-layout form .fi-sc-actions .fi-ac,
                        html[dir="rtl"] .fi-layout form > .fi-ac {
                            left: calc(50% - (var(--sidebar-width, 16rem) / 2)) !important;
                        }

                        /* Center the buttons inside the sticky container */
                        .fi-layout form .fi-form-actions > div,
                        .fi-layout form .fi-sc-actions .fi-ac > div,
                        .fi-layout form > .fi-ac > div {
                            display: flex !important;
                            justify-content: center !important;
                            align-items: center !important;
                            width: auto !important;
                            flex-grow: 0 !important;
                            gap: 1rem !important;
                        }

                        /* Dark mode overrides */
                        .dark .fi-layout form .fi-form-actions,
                        .dark .fi-layout form .fi-sc-actions .fi-ac,
                        .dark .fi-layout form > .fi-ac {
                            background-color: rgba(24, 24, 27, 0.45) !important;
                            border: 1px solid rgba(255, 255, 255, 0.1) !important;
                            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.5), 0 4px 6px -4px rgba(0, 0, 0, 0.5) !important;
                        }

                        /* Reset fixed styling inside modals / slideovers to avoid breaking modal forms */
                        .fi-modal-window form .fi-form-actions,
                        .fi-modal-window form .fi-sc-actions .fi-ac,
                        .fi-modal-window form > .fi-ac,
                        .fi-modal form .fi-form-actions,
                        .fi-modal form .fi-sc-actions .fi-ac,
                        .fi-modal form > .fi-ac {
                            position: static !important;
                            background-color: transparent !important;
                            backdrop-filter: none !important;
                            -webkit-backdrop-filter: none !important;
                            box-shadow: none !important;
                            border: none !important;
                            padding: 0 !important;
                            margin: 0 !important;
                            transform: none !important;
                        }
                    </style>
                '
            )
            ->plugins([
                \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make()
                    ->navigationGroup(fn () => __('System Settings'))
                    ->navigationSort(80),
            ])
            ->navigationGroups([
                \Filament\Navigation\NavigationGroup::make()
                    ->label(fn () => __('Home Page')),
                \Filament\Navigation\NavigationGroup::make()
                    ->label(fn () => __('Content'))
                    ->icon('heroicon-o-document-text'),
                \Filament\Navigation\NavigationGroup::make()
                    ->label(fn () => __('Leads'))
                    ->icon('heroicon-o-inbox-arrow-down')
                    ->collapsible(false),
                \Filament\Navigation\NavigationGroup::make()
                    ->label(fn () => __('Careers'))
                    ->icon('heroicon-o-briefcase'),
                \Filament\Navigation\NavigationGroup::make()
                    ->label(fn () => __('Header')),
                \Filament\Navigation\NavigationGroup::make()
                    ->label(fn () => __('Footer')),
                \Filament\Navigation\NavigationGroup::make()
                    ->label(fn () => __('Settings')),
                \Filament\Navigation\NavigationGroup::make()
                    ->label('AI Content Tools')
                    ->icon('heroicon-o-cpu-chip')
                    ->collapsed(),
                \Filament\Navigation\NavigationGroup::make()
                    ->label(fn () => __('System Settings')),
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                \App\Filament\Widgets\StatsOverview::class,
                \App\Filament\Widgets\RecentLeadsWidget::class,
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
