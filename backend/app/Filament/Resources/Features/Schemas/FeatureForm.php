<?php

namespace App\Filament\Resources\Features\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class FeatureForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Tabs::make('Translations')
                    ->tabs([
                        \Filament\Schemas\Components\Tabs\Tab::make('English')
                            ->schema([
                                TextInput::make('title.en')
                                    ->label('Title')
                                    ->required(),
                                Textarea::make('description.en')
                                    ->label('Description')
                                    ->required()
                                    ->columnSpanFull(),
                            ]),
                        \Filament\Schemas\Components\Tabs\Tab::make('Arabic')
                            ->schema([
                                TextInput::make('title.ar')
                                    ->label('Title')
                                    ->required(),
                                Textarea::make('description.ar')
                                    ->label('Description')
                                    ->required()
                                    ->columnSpanFull(),
                            ]),
                    ])->columnSpanFull(),
                TextInput::make('icon')
                    ->default(null),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
