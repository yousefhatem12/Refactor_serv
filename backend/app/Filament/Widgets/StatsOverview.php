<?php

namespace App\Filament\Widgets;

use App\Models\ContactMessage;
use App\Models\Product;
use App\Models\Project;
use App\Models\Service;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        return [
            Stat::make(__('Services / الخدمات'), Service::count())
                ->description(__('Total services configured / إجمالي الخدمات'))
                ->descriptionIcon('heroicon-m-briefcase')
                ->color('primary'),

            Stat::make(__('Projects / المشروعات'), Project::count())
                ->description(__('Total portfolio items / إجمالي الأعمال'))
                ->descriptionIcon('heroicon-m-document-text')
                ->color('success'),

            Stat::make(__('Products / المنتجات'), Product::count())
                ->description(__('Total digital products / إجمالي المنتجات'))
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('info'),

            Stat::make(__('Messages / الرسائل'), ContactMessage::count())
                ->description(__('Messages received / الرسائل الواردة'))
                ->descriptionIcon('heroicon-m-envelope')
                ->color('warning'),
        ];
    }
}
