<?php

namespace App\Filament\Resources\ProjectConsultationResource\Pages;

use App\Filament\Resources\ProjectConsultationResource;
use Filament\Resources\Pages\EditRecord;

class ViewProjectConsultation extends EditRecord
{
    protected static string $resource = ProjectConsultationResource::class;

    protected static ?string $title = 'View Project Consultation';

    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);

        /** @var \App\Models\ProjectConsultation $recordModel */
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
