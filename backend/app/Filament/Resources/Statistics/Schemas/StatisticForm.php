<?php

namespace App\Filament\Resources\Statistics\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class StatisticForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('label')
                    ->required(),
                TextInput::make('count')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('icon')
                    ->default(null),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
