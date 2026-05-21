<?php
namespace App\Filament\Resources\ServiceTemplateResource\Pages;
use App\Filament\Resources\ServiceTemplateResource;
use Filament\Resources\Pages\ListRecords;
class ListServiceTemplates extends ListRecords
{
    protected static string $resource = ServiceTemplateResource::class;
    protected function getHeaderActions(): array { return [\Filament\Actions\CreateAction::make()]; }
}
