<?php

namespace App\Filament\Resources\AiCredentialResource\Pages;

use App\Filament\Resources\AiCredentialResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAiCredentials extends ListRecords
{
    protected static string $resource = AiCredentialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
