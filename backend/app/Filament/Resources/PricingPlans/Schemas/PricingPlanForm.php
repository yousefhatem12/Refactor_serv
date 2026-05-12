<?php

namespace App\Filament\Resources\PricingPlans\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PricingPlanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('English Translations')
                    ->schema([
                        TextInput::make('name.en')
                            ->label('Name (EN)')
                            ->required(),
                        TextInput::make('period.en')
                            ->label('Period (EN)')
                            ->placeholder('e.g., /month'),
                        \Filament\Forms\Components\Repeater::make('features.en')
                            ->label('Features (EN)')
                            ->simple(TextInput::make('feature')->required()),
                        TextInput::make('button_text.en')
                            ->label('Button Text (EN)')
                            ->default('Get Started'),
                    ])->columns(2),

                \Filament\Schemas\Components\Section::make('Arabic Translations')
                    ->schema([
                        TextInput::make('name.ar')
                            ->label('Name (AR)')
                            ->required(),
                        TextInput::make('period.ar')
                            ->label('Period (AR)')
                            ->placeholder('e.g., /شهر'),
                        \Filament\Forms\Components\Repeater::make('features.ar')
                            ->label('Features (AR)')
                            ->simple(TextInput::make('feature')->required()),
                        TextInput::make('button_text.ar')
                            ->label('Button Text (AR)')
                            ->default('ابدأ الآن'),
                    ])->columns(2),

                \Filament\Schemas\Components\Section::make('Settings')
                    ->schema([
                        \Filament\Forms\Components\Select::make('service_id')
                            ->label('Service')
                            ->relationship('service', 'name')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->name['en'] ?? $record->name['ar'])
                            ->searchable()
                            ->preload()
                            ->required(),
                        TextInput::make('price')
                            ->required()
                            ->numeric(),
                        TextInput::make('currency')
                            ->required()
                            ->default('$'),
                        TextInput::make('button_url')
                            ->required()
                            ->default('/contact-us'),
                        TextInput::make('sort_order')
                            ->required()
                            ->numeric()
                            ->default(0),
                        Toggle::make('is_popular')
                            ->default(false),
                        Toggle::make('is_active')
                            ->default(true),
                    ])->columns(2),
            ]);
    }
}
