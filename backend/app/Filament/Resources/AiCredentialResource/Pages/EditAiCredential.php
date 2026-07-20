<?php

namespace App\Filament\Resources\AiCredentialResource\Pages;

use App\Filament\Resources\AiCredentialResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Crypt;

class EditAiCredential extends EditRecord
{
    protected static string $resource = AiCredentialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (!empty($data['new_api_token'])) {
            $data['api_token'] = Crypt::encryptString($data['new_api_token']);
            $data['token_hint'] = substr($data['new_api_token'], -4);
            $data['token_updated_at'] = now();
        } else {
            // Keep existing token
            unset($data['api_token']);
        }
        unset($data['new_api_token']);
        return $data;
    }
}
