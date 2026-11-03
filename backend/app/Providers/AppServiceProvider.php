<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use BezhanSalleh\LanguageSwitch\LanguageSwitch;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(\Filament\Notifications\Notification::class, \App\Notifications\CustomFilamentNotification::class);
        $this->app->singleton(\App\Contracts\Api\ApiCacheServiceInterface::class, \App\Services\Api\Support\ApiCacheService::class);
        $this->app->singleton(\App\Contracts\Notifications\AdminNotificationServiceInterface::class, \App\Services\Notifications\AdminNotificationService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Keep the AI migrations in the normal migration lifecycle. They live
        // in a subdirectory, so Laravel does not discover them automatically.
        $this->loadMigrationsFrom(database_path('migrations/ai'));

        // Configure outbound HTTP client to negotiate HTTP/3 or HTTP/2 with TCP keep-alive.
        \App\Services\Http\OutboundHttpClientConfigurator::configure();

        RateLimiter::for('auth', function (Request $request) {
            $email = strtolower(trim((string) $request->input('email')));

            return Limit::perMinute(5)->by($request->ip() . '|' . $email);
        });

        RateLimiter::for('public-forms', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip());
        });

        RateLimiter::for('public-uploads', function (Request $request) {
            return Limit::perMinute(3)->by($request->ip());
        });

        // General rate limit for all public GET endpoints.
        // Allows normal browsing (120 req/min) while blocking scrapers and
        // DDoS bursts that bypass Cloudflare. The stricter POST limits above
        // are applied in addition to this for form/upload endpoints.
        RateLimiter::for('public-read', function (Request $request) {
            return Limit::perMinute(120)->by($request->ip());
        });

        // Implicitly grant "super_admin" role all permissions
        \Illuminate\Support\Facades\Gate::before(function ($user, $ability) {
            return $user->hasRole('super_admin') ? true : null;
        });

        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->locales(['ar', 'en']); // Arabic and English
        });

        Event::listen(Failed::class, function (Failed $event): void {
            Log::warning('Authentication attempt failed.', [
                'guard' => $event->guard,
                'user_id' => $event->user?->getAuthIdentifier(),
                'email' => is_array($event->credentials) ? ($event->credentials['email'] ?? null) : null,
                'ip' => request()?->ip(),
                'user_agent' => request()?->userAgent(),
            ]);
        });

        Event::listen(Lockout::class, function (Lockout $event): void {
            Log::warning('Authentication lockout triggered.', [
                'email' => $event->request->input('email'),
                'ip' => $event->request->ip(),
                'user_agent' => $event->request->userAgent(),
            ]);
        });

        // NOTE: Per-model cache invalidation is handled in each model's booted() method.
        // A global Cache::flush() loop was removed from here because it wiped ALL cache
        // on every single Filament save/delete, causing thundering-herd on the database.
    }
}
