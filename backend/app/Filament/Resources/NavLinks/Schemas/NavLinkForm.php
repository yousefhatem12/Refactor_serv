<?php

namespace App\Filament\Resources\NavLinks\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class NavLinkForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Grid::make(2)
                    ->schema([
                        TextInput::make('label.en')
                            ->label('Label (English)')
                            ->required(),
                        TextInput::make('label.ar')
                            ->label('Label (Arabic)')
                            ->required(),
                    ]),
                TextInput::make('url')
                    ->required(),
                \Filament\Forms\Components\Select::make('parent_id')
                    ->label('Parent Link')
                    ->relationship('parent', 'label->en')
                    ->nullable()
                    ->searchable(),
                TextInput::make('sort_order')
                    ->required()
                    ->numeric()
                    ->default(0),
                Toggle::make('is_active')
                    ->default(true),
            ]);
    }
}
