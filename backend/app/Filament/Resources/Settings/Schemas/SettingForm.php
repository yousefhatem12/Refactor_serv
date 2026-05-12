<?php

namespace App\Filament\Resources\Settings\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class SettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('key')
                    ->required()
                    ->disabled(fn($record) => $record !== null), // Key should not be changed once created

                \Filament\Forms\Components\Select::make('type')
                    ->options([
                        'text' => 'Text',
                        'textarea' => 'Long Text',
                        'image' => 'Image',
                        'video' => 'Video',
                        'toggle' => 'On/Off',
                    ])
                    ->required()
                    ->live(),

                \Filament\Forms\Components\Radio::make('value_source')
                    ->label('Source')
                    ->options([
                        'link' => 'External Link',
                        'file' => 'Upload File',
                    ])
                    ->default('link')
                    ->visible(fn($get) => in_array($get('type'), ['image', 'video']))
                    ->live(),

                TextInput::make('text_value')
                    ->label('Link URL / Text')
                    ->visible(fn($get) => in_array($get('type'), ['text']) || (in_array($get('type'), ['image', 'video']) && $get('value_source') === 'link'))
                    ->placeholder(fn($get) => $get('type') === 'video' ? 'YouTube link or external URL' : 'Enter URL')
                    ->required(fn($get) => in_array($get('type'), ['text']) || (in_array($get('type'), ['image', 'video']) && $get('value_source') === 'link')),

                Textarea::make('textarea_value')
                    ->label('Value')
                    ->visible(fn($get) => $get('type') === 'textarea')
                    ->columnSpanFull()
                    ->required(fn($get) => $get('type') === 'textarea'),

                \Filament\Forms\Components\FileUpload::make('file_value')
                    ->label('Upload File')
                    ->directory('settings')
                    ->visible(fn($get) => in_array($get('type'), ['image', 'video']) && $get('value_source') === 'file')
                    ->saveUploadedFileUsing(function ($file, $get) {
                        if ($get('type') === 'image') {
                            return \App\Services\ImageService::convertToWebp($file, 'settings');
                        }
                        return $file->store('settings', 'public');
                    })
                    ->maxSize(512000)
                    ->columnSpanFull()
                    ->required(fn($get) => in_array($get('type'), ['image', 'video']) && $get('value_source') === 'file'),

                \Filament\Forms\Components\Toggle::make('toggle_value')
                    ->label('Enabled')
                    ->visible(fn($get) => $get('type') === 'toggle'),
            ]);
    }
}
