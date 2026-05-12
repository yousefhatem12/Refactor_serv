<?php

namespace App\Filament\Resources\ContactMessageResource\Pages;

use App\Filament\Resources\ContactMessageResource;
use Filament\Resources\Pages\ListRecords;

class ListContactMessages extends ListRecords
{
    protected static string $resource = ContactMessageResource::class;

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
