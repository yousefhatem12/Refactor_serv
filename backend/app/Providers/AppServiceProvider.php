<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use BezhanSalleh\LanguageSwitch\LanguageSwitch;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(\Filament\Notifications\Notification::class, \App\Notifications\CustomFilamentNotification::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Keep the AI migrations in the normal migration lifecycle. They live
        // in a subdirectory, so Laravel does not discover them automatically.
        $this->loadMigrationsFrom(database_path('migrations/ai'));

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

        // Implicitly grant "super_admin" role all permissions
        \Illuminate\Support\Facades\Gate::before(function ($user, $ability) {
            return $user->hasRole('super_admin') ? true : null;
        });

        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->locales(['ar', 'en']); // Arabic and English
        });

        // Automatically clear cache when any frontend-related model is updated
        $cachedModels = [
            \App\Models\Service::class, \App\Models\Project::class, \App\Models\Setting::class,
            \App\Models\Post::class, \App\Models\Faq::class, \App\Models\Testimonial::class,
            \App\Models\TeamMember::class, \App\Models\Feature::class, \App\Models\Statistic::class,
            \App\Models\FooterLink::class, \App\Models\SocialLink::class, \App\Models\NavLink::class,
            \App\Models\Page::class, \App\Models\PricingPlan::class,
            \App\Models\Product::class, \App\Models\BankAccount::class,
        ];

        foreach ($cachedModels as $model) {
            if (class_exists($model)) {
                $model::saved(fn () => \Illuminate\Support\Facades\Cache::flush());
                $model::deleted(fn () => \Illuminate\Support\Facades\Cache::flush());
            }
        }
    }
}
