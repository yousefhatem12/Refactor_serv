<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Filament\Pages\ManageAboutPage;
use App\Filament\Resources\ServiceResource;
use App\Filament\Resources\ProjectResource;
use App\Filament\Resources\PostResource;
use App\Filament\Pages\ManageFaqPage;
use App\Filament\Resources\FaqResource;
use App\Filament\Pages\ManageContactPage;
use App\Filament\Resources\ContactMessageResource;
use App\Filament\Resources\Products\ProductResource;
use App\Filament\Resources\Careers\CareerResource;
use App\Filament\Resources\ProjectCategoryResource;
use App\Filament\Resources\CareerApplications\CareerApplicationResource;

class SitePages extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-duplicate';
    protected static ?int $navigationSort = 20;
    public static function getNavigationLabel(): string { return __('Pages'); }
    public static function getNavigationGroup(): ?string { return __('Pages'); }

    protected string $view = 'filament.pages.site-pages';

    public function getTitle(): string
    {
        return __('Pages');
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
                <span>' . __('Pages') . '</span>
            </span>
        ');
    }

    protected function getViewData(): array
    {
        return [
            'cards' => [
                [
                    'title' => __('About Us'),
                    'description' => __('Manage the about us page content and sections.'),
                    'icon' => 'heroicon-o-information-circle',
                    'url' => ManageAboutPage::getUrl(),
                    'color' => 'primary',
                ],
                [
                    'title' => __('Services'),
                    'description' => __('Manage all the services offered.'),
                    'icon' => 'heroicon-o-briefcase',
                    'url' => ServiceResource::getUrl(),
                    'color' => 'success',
                ],
                [
                    'title' => __('Our Products'),
                    'description' => __('Manage showcase products and details.'),
                    'icon' => 'heroicon-o-shopping-bag',
                    'url' => ProductResource::getUrl(),
                    'color' => 'primary',
                ],
                [
                    'title' => __('Our Work'),
                    'description' => __('Manage portfolio projects and works.'),
                    'icon' => 'heroicon-o-folder-open',
                    'url' => ProjectResource::getUrl(),
                    'color' => 'warning',
                ],
                [
                    'title' => __('Portfolio Categories'),
                    'description' => __('Manage categories for the portfolio section.'),
                    'icon' => 'heroicon-o-tag',
                    'url' => ProjectCategoryResource::getUrl(),
                    'color' => 'warning',
                ],
                [
                    'title' => __('Blog'),
                    'description' => __('Manage blog posts and articles.'),
                    'icon' => 'heroicon-o-newspaper',
                    'url' => PostResource::getUrl(),
                    'color' => 'danger',
                ],
                [
                    'title' => app()->getLocale() === 'ar' ? 'تصنيفات المقالات' : 'Blog Categories',
                    'description' => app()->getLocale() === 'ar' ? 'إدارة تصنيفات مقالات المدونة.' : 'Manage categories for blog posts.',
                    'icon' => 'heroicon-o-tag',
                    'url' => \App\Filament\Resources\BlogCategoryResource::getUrl(),
                    'color' => 'danger',
                ],
                [
                    'title' => __('Careers'),
                    'description' => __('Manage job postings and openings.'),
                    'icon' => 'heroicon-o-briefcase',
                    'url' => CareerResource::getUrl(),
                    'color' => 'info',
                ],
                [
                    'title' => __('Career Applications'),
                    'description' => __('View and review received job applications.'),
                    'icon' => 'heroicon-o-user-group',
                    'url' => CareerApplicationResource::getUrl(),
                    'color' => 'success',
                ],
                [
                    'title' => __('FAQ Page'),
                    'description' => __('Manage FAQ page details and FAQ list items.'),
                    'icon' => 'heroicon-o-question-mark-circle',
                    'url' => ManageFaqPage::getUrl(),
                    'color' => 'info',
                ],
                [
                    'title' => __('Contact Us'),
                    'description' => __('Manage the contact page content and details.'),
                    'icon' => 'heroicon-o-phone-arrow-up-right',
                    'url' => ManageContactPage::getUrl(),
                    'color' => 'success',
                ],
                [
                    'title' => __('Contact Messages'),
                    'description' => __('View and manage incoming contact messages.'),
                    'icon' => 'heroicon-o-envelope',
                    'url' => ContactMessageResource::getUrl(),
                    'color' => 'warning',
                ],
                [
                    'title' => __('Quotations'),
                    'description' => __('View and manage incoming quotation requests.'),
                    'icon' => 'heroicon-o-document-text',
                    'url' => \App\Filament\Resources\QuotationResource::getUrl(),
                    'color' => 'success',
                ],
                [
                    'title' => app()->getLocale() === 'ar' ? 'استشارات المشاريع' : 'Project Consultations',
                    'description' => app()->getLocale() === 'ar' ? 'عرض وإدارة طلبات استشارات المشاريع الواردة.' : 'View and manage incoming project consultation requests.',
                    'icon' => 'heroicon-o-chat-bubble-left-right',
                    'url' => \App\Filament\Resources\ProjectConsultationResource::getUrl(),
                    'color' => 'primary',
                ],
            ]
        ];
    }
}
