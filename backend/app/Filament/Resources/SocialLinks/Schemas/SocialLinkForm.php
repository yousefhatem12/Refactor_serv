<?php

namespace App\Filament\Resources\SocialLinks\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class SocialLinkForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\Select::make('platform')
                    ->options([
                        'facebook' => 'Facebook',
                        'twitter' => 'Twitter (X)',
                        'instagram' => 'Instagram',
                        'linkedin' => 'LinkedIn',
                        'youtube' => 'YouTube',
                        'tiktok' => 'TikTok',
                    ])
                    ->required(),
                \Filament\Forms\Components\TextInput::make('url')
                    ->url()
                    ->required(),
                \Filament\Forms\Components\Toggle::make('is_active')
                    ->default(true)
                    ->required(),
            ]);
    }
}
