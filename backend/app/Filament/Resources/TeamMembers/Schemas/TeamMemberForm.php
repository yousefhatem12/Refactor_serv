<?php

namespace App\Filament\Resources\TeamMembers\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class TeamMemberForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('role')
                    ->required(),
                Textarea::make('bio')
                    ->default(null)
                    ->columnSpanFull(),
                FileUpload::make('image')
                    ->image()
                    ->directory('team')
                    ->saveUploadedFileUsing(fn ($file) => \App\Services\ImageService::convertToWebp($file, 'team')),
                TextInput::make('facebook_url')
                    ->url()
                    ->default(null),
                TextInput::make('linkedin_url')
                    ->url()
                    ->default(null),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
