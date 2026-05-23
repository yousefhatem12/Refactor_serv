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

        if ($this->record->status === 'unread' || empty($this->record->status)) {
            $this->record->update([
                'status' => 'read',
            ]);
        }

        parent::mount($record);
    }
}
