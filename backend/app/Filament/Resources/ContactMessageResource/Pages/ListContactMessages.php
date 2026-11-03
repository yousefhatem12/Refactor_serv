<?php

namespace App\Filament\Resources\ContactMessageResource\Pages;

use App\Filament\Resources\ContactMessageResource;
use Filament\Resources\Pages\ListRecords;

class ListContactMessages extends ListRecords
{
    protected static string $resource = ContactMessageResource::class;

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
            'inquiry' => \Filament\Schemas\Components\Tabs\Tab::make('Inquiries')
                ->modifyQueryUsing(fn (\Illuminate\Database\Eloquent\Builder $query) => $query->where('type', 'inquiry')),
            'pricing' => \Filament\Schemas\Components\Tabs\Tab::make('Pricing')
                ->modifyQueryUsing(fn (\Illuminate\Database\Eloquent\Builder $query) => $query->where('type', 'pricing')),
            'complaint' => \Filament\Schemas\Components\Tabs\Tab::make('Complaints')
                ->modifyQueryUsing(fn (\Illuminate\Database\Eloquent\Builder $query) => $query->where('type', 'complaint')),
            'support' => \Filament\Schemas\Components\Tabs\Tab::make('Support')
                ->modifyQueryUsing(fn (\Illuminate\Database\Eloquent\Builder $query) => $query->where('type', 'support')),
            'job' => \Filament\Schemas\Components\Tabs\Tab::make('Jobs')
                ->modifyQueryUsing(fn (\Illuminate\Database\Eloquent\Builder $query) => $query->where('type', 'job')),
        ];
    }
}
