<?php

namespace App\Filament\Resources\QuotationResource\Pages;

use App\Filament\Resources\QuotationResource;
use Filament\Resources\Pages\EditRecord;

class ViewQuotation extends EditRecord
{
    protected static string $resource = QuotationResource::class;

    protected static ?string $title = 'View Quotation Request';

    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);

        /** @var \App\Models\Quotation $recordModel */
        $recordModel = $this->record;
        if ($recordModel->status === 'unread' || empty($recordModel->status)) {
            $recordModel->update([
                'status' => 'read',
            ]);
        }

        parent::mount($record);
    }
}
