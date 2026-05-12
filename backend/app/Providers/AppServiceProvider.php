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
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
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
        ];

        foreach ($cachedModels as $model) {
            if (class_exists($model)) {
                $model::saved(fn () => \Illuminate\Support\Facades\Cache::flush());
                $model::deleted(fn () => \Illuminate\Support\Facades\Cache::flush());
            }
        }
    }
}
