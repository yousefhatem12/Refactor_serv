<?php

namespace App\Filament\Resources\FooterLinks\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class FooterLinkForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\TextInput::make('label')
                    ->required(),
                \Filament\Forms\Components\TextInput::make('url')
                    ->required(),
                \Filament\Forms\Components\Select::make('column_name')
                    ->options([
                        'useful_links' => 'Useful Links',
                        'quick_links' => 'Quick Links',
                        'offers' => 'Offers',
                        'company' => 'Company',
                    ])
                    ->required(),
                \Filament\Forms\Components\TextInput::make('sort_order')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
