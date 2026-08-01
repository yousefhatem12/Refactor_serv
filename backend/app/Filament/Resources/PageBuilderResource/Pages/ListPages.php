<?php

namespace App\Filament\Resources\PageBuilderResource\Pages;

use App\Filament\Resources\PageBuilderResource;
use App\Filament\Pages\AiPageBuilder;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPages extends ListRecords
{
    protected static string $resource = PageBuilderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('aiPageBuilder')
                ->label('AI Page Builder')
                ->icon('heroicon-o-sparkles')
                ->url(AiPageBuilder::getUrl()),
            Actions\CreateAction::make(),
        ];
    }
}
