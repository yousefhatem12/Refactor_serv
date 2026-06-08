<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use BezhanSalleh\LanguageSwitch\LanguageSwitch;

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
        // Run migration automatically if project slug column is missing
        if (!\Illuminate\Support\Facades\Schema::hasColumn('projects', 'slug')) {
            try {
                \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Migration failed: ' . $e->getMessage());
            }
        }

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
