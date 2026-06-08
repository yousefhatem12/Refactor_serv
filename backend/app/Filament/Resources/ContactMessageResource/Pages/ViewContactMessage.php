<?php

namespace App\Filament\Resources\ContactMessageResource\Pages;

use App\Filament\Resources\ContactMessageResource;
use Filament\Resources\Pages\EditRecord;

class ViewContactMessage extends EditRecord
{
    protected static string $resource = ContactMessageResource::class;

    protected static ?string $title = 'View Contact Message';

    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);

        /** @var \App\Models\ContactMessage $recordModel */
        $recordModel = $this->record;
        if ($recordModel->status === 'unread') {
            $recordModel->update([
                'status' => 'read',
                'read_at' => now(),
            ]);
        }

        parent::mount($record);
    }
}
