<?php

namespace App\Filament\Resources\Testimonials\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class TestimonialForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('client_name')
                    ->required(),
                TextInput::make('position')
                    ->default(null),
                Textarea::make('review')
                    ->required()
                    ->columnSpanFull(),
                FileUpload::make('image')
                    ->image(),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
