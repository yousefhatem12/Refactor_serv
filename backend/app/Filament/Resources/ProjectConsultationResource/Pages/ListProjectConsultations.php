<?php

namespace App\Filament\Resources\ProjectConsultationResource\Pages;

use App\Filament\Resources\ProjectConsultationResource;
use Filament\Resources\Pages\ListRecords;

class ListProjectConsultations extends ListRecords
{
    protected static string $resource = ProjectConsultationResource::class;

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return new \Illuminate\Support\HtmlString('
            <span class="inline-flex items-center gap-3">
                <a href="' . \App\Filament\Pages\SitePages::getUrl() . '" class="inline-flex items-center justify-center rounded-md bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-400 transition" style="width: 24px; height: 24px; min-width: 24px; min-height: 24px; display: inline-flex; align-items: center; justify-content: center;">
                    <svg class="rtl:rotate-180" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width: 14px; height: 14px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                    </svg>
                </a>
                <span>' . parent::getHeading() . '</span>
            </span>
        ');
    }

    public function getTabs(): array
    {
        return [
            'all' => \Filament\Schemas\Components\Tabs\Tab::make('All'),
            'unread' => \Filament\Schemas\Components\Tabs\Tab::make('Unread')
                ->modifyQueryUsing(fn (\Illuminate\Database\Eloquent\Builder $query) => $query->where('status', 'unread')),
            'read' => \Filament\Schemas\Components\Tabs\Tab::make('Read')
                ->modifyQueryUsing(fn (\Illuminate\Database\Eloquent\Builder $query) => $query->where('status', 'read')),
        ];
    }
}
