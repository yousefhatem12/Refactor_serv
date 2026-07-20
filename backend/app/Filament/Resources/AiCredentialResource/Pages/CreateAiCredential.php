<?php

namespace App\Filament\Resources\AiCredentialResource\Pages;

use App\Filament\Resources\AiCredentialResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Crypt;

class CreateAiCredential extends CreateRecord
{
    protected static string $resource = AiCredentialResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (!empty($data['new_api_token'])) {
            $data['api_token'] = Crypt::encryptString($data['new_api_token']);
            $data['token_hint'] = substr($data['new_api_token'], -4);
            $data['token_updated_at'] = now();
        }
        unset($data['new_api_token']);
        return $data;
    }
}
