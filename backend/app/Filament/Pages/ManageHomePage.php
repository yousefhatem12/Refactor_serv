<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Filament\Pages\ManageHomeHero;
use App\Filament\Pages\ManageHomeCTA;
use App\Filament\Pages\ManageHomeFeaturesSettings;
use App\Filament\Resources\Features\FeatureResource;
use App\Filament\Resources\Testimonials\TestimonialResource;
use App\Filament\Resources\TeamMembers\TeamMemberResource;

class ManageHomePage extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-home';
    public static function getNavigationLabel(): string { return __('Home Page'); }
    public static function getNavigationGroup(): ?string { return __('Home Page'); }
    protected static ?int $navigationSort = 10;
    
    protected string $view = 'filament.pages.site-pages';

    public function getTitle(): string
    {
        return __('Home Page Sections');
    }

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return new \Illuminate\Support\HtmlString('
            <span class="inline-flex items-center gap-3">
                <a href="' . url('/admin') . '" class="inline-flex items-center justify-center rounded-md bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-400 transition" style="width: 24px; height: 24px; min-width: 24px; min-height: 24px; display: inline-flex; align-items: center; justify-content: center;">
                    <svg class="rtl:rotate-180" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width: 14px; height: 14px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                    </svg>
                </a>
                <span>' . __('Home Page Sections') . '</span>
            </span>
        ');
    }

    protected function getViewData(): array
    {
        return [
            'cards' => [
                [
                    'title' => __('Hero Section'),
                    'description' => __('Manage the main hero section, SEO, and dynamic blocks.'),
                    'icon' => 'heroicon-o-sparkles',
                    'url' => ManageHomeHero::getUrl(),
                    'color' => 'primary',
                ],
                [
                    'title' => __('Features Settings'),
                    'description' => __('Manage the titles and descriptions of the features section.'),
                    'icon' => 'heroicon-o-adjustments-horizontal',
                    'url' => ManageHomeFeaturesSettings::getUrl(),
                    'color' => 'info',
                ],
                [
                    'title' => __('Features List'),
                    'description' => __('Manage the individual features.'),
                    'icon' => 'heroicon-o-star',
                    'url' => FeatureResource::getUrl(),
                    'color' => 'success',
                ],
                [
                    'title' => __('Testimonials'),
                    'description' => __('Manage client testimonials.'),
                    'icon' => 'heroicon-o-chat-bubble-left-right',
                    'url' => TestimonialResource::getUrl(),
                    'color' => 'warning',
                ],
                [
                    'title' => __('Company Team'),
                    'description' => __('Manage team members.'),
                    'icon' => 'heroicon-o-users',
                    'url' => TeamMemberResource::getUrl(),
                    'color' => 'primary',
                ],
                [
                    'title' => __('Call To Action (CTA)'),
                    'description' => __('Manage the CTA section content at the bottom of the page.'),
                    'icon' => 'heroicon-o-megaphone',
                    'url' => ManageHomeCTA::getUrl(),
                    'color' => 'danger',
                ],
                [
                    'title' => __('Contact Us'),
                    'description' => __('Manage the contact page content, phone numbers, and addresses.'),
                    'icon' => 'heroicon-o-phone-arrow-up-right',
                    'url' => \App\Filament\Pages\ManageContactPage::getUrl(),
                    'color' => 'success',
                ],
            ]
        ];
    }
}
