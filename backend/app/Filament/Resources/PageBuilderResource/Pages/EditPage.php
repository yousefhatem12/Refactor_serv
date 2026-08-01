<?php

namespace App\Filament\Resources\PageBuilderResource\Pages;

use App\Filament\Resources\PageBuilderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPage extends EditRecord
{
    protected static string $resource = PageBuilderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if ($this->record->ai_generated) {
            $data['ai_status'] = !empty($data['is_published'])
                ? 'approved'
                : ($this->record->ai_status === 'approved' ? 'pending_review' : $this->record->ai_status);
        }

        return $data;
    }
}
