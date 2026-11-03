<?php

namespace App\Filament\Resources\Settings\Pages;

use App\Filament\Resources\Settings\SettingResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSetting extends EditRecord
{
    protected static string $resource = SettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $type = $data['type'] ?? '';
        $source = $data['value_source'] ?? 'link';
        
        if (in_array($type, ['image', 'video']) && $source === 'file') {
            $data['file_value'] = $data['value'];
        } elseif (in_array($type, ['text']) || (in_array($type, ['image', 'video']) && $source === 'link')) {
            $data['text_value'] = $data['value'];
        } elseif ($type === 'textarea') {
            $data['textarea_value'] = $data['value'];
        } elseif ($type === 'toggle') {
            $data['toggle_value'] = (bool) $data['value'];
        }
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $type = $data['type'] ?? '';
        $source = $data['value_source'] ?? 'link';

        if (in_array($type, ['image', 'video']) && $source === 'file') {
            if (array_key_exists('file_value', $data)) {
                $data['value'] = is_array($data['file_value']) ? reset($data['file_value']) : $data['file_value'];
            }
        } elseif (in_array($type, ['text']) || (in_array($type, ['image', 'video']) && $source === 'link')) {
            if (array_key_exists('text_value', $data)) {
                $data['value'] = $data['text_value'];
            }
        } elseif ($type === 'textarea') {
            if (array_key_exists('textarea_value', $data)) {
                $data['value'] = $data['textarea_value'];
            }
        } elseif ($type === 'toggle') {
            if (array_key_exists('toggle_value', $data)) {
                $data['value'] = $data['toggle_value'] ? '1' : '0';
            }
        }
        
        unset($data['text_value'], $data['textarea_value'], $data['file_value'], $data['toggle_value']);
        
        return $data;
    }
}
