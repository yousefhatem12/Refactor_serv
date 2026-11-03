<?php

namespace App\Filament\Widgets;

use App\Models\CareerApplication;
use App\Models\ContactMessage;
use App\Models\ProjectConsultation;
use App\Models\Quotation;
use App\Models\Service;
use App\Models\Project;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected ?string $pollingInterval = '60s';

    protected function getStats(): array
    {
        $unreadMessages    = Cache::remember('dash_unread_messages',     60, fn () => ContactMessage::where('status', 'unread')->count());
        $unreadQuotations  = Cache::remember('dash_unread_quotations',   60, fn () => Quotation::where('status', 'unread')->count());
        $unreadConsults    = Cache::remember('dash_unread_consultations', 60, fn () => ProjectConsultation::where('status', 'unread')->count());
        $pendingApps       = Cache::remember('dash_pending_career_apps',  60, fn () => CareerApplication::where('status', 'pending')->count());

        $totalServices = Cache::remember('dash_total_services', 300, fn () => Service::count());
        $totalProjects = Cache::remember('dash_total_projects', 300, fn () => Project::count());

        return [
            Stat::make(__('Unread Messages'), $unreadMessages)
                ->description($unreadMessages > 0
                    ? __('Needs your attention')
                    : __('All clear'))
                ->descriptionIcon($unreadMessages > 0 ? 'heroicon-m-exclamation-circle' : 'heroicon-m-check-circle')
                ->color($unreadMessages > 0 ? 'danger' : 'success')
                ->url(\App\Filament\Resources\ContactMessageResource::getUrl('index')),

            Stat::make(__('Pending Quotations'), $unreadQuotations)
                ->description($unreadQuotations > 0
                    ? __('Awaiting review')
                    : __('All reviewed'))
                ->descriptionIcon($unreadQuotations > 0 ? 'heroicon-m-clock' : 'heroicon-m-check-circle')
                ->color($unreadQuotations > 0 ? 'warning' : 'success')
                ->url(\App\Filament\Resources\QuotationResource::getUrl('index')),

            Stat::make(__('Consultations'), $unreadConsults)
                ->description($unreadConsults > 0
                    ? __('New consultations')
                    : __('All reviewed'))
                ->descriptionIcon($unreadConsults > 0 ? 'heroicon-m-chat-bubble-left-right' : 'heroicon-m-check-circle')
                ->color($unreadConsults > 0 ? 'info' : 'success')
                ->url(\App\Filament\Resources\ProjectConsultationResource::getUrl('index')),

            Stat::make(__('Career Applications'), $pendingApps)
                ->description($pendingApps > 0
                    ? __('Pending review')
                    : __('No pending'))
                ->descriptionIcon($pendingApps > 0 ? 'heroicon-m-briefcase' : 'heroicon-m-check-circle')
                ->color($pendingApps > 0 ? 'primary' : 'success')
                ->url(\App\Filament\Resources\CareerApplications\CareerApplicationResource::getUrl('index')),

            Stat::make(__('Services'), $totalServices)
                ->description(__('Published services'))
                ->descriptionIcon('heroicon-m-cog-6-tooth')
                ->color('gray'),

            Stat::make(__('Portfolio'), $totalProjects)
                ->description(__('Portfolio projects'))
                ->descriptionIcon('heroicon-m-photo')
                ->color('gray'),
        ];
    }
}
