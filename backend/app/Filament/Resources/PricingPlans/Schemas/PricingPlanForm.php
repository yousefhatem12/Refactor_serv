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
                \Filament\Forms\Components\Select::make('service_id')
                    ->label('Service')
                    ->relationship('service', 'name')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->name['en'] ?? $record->name['ar'])
                    ->searchable()
                    ->preload()
                    ->required()
                    ->columnSpanFull(),

                \Filament\Schemas\Components\Section::make('English Details')
                    ->schema([
                        TextInput::make('name.en')
                            ->label('Title (EN)')
                            ->required(),
                        Textarea::make('features.en')
                            ->label('Description (EN)')
                            ->placeholder("e.g. Price: 39 SAR\nPeriod: /month\nssl free...")
                            ->rows(4),
                    ])->columns(1),

                \Filament\Schemas\Components\Section::make('Arabic Details')
                    ->schema([
                        TextInput::make('name.ar')
                            ->label('Title (AR)')
                            ->required(),
                        Textarea::make('features.ar')
                            ->label('Description (AR)')
                            ->placeholder("مثال: السعر: 39 ريال\nالمدة: /شهر\nشهادة أمان مجانية...")
                            ->rows(4),
                    ])->columns(1),

                \Filament\Schemas\Components\Section::make('Advanced Settings (Optional)')
                    ->collapsed()
                    ->schema([
                        TextInput::make('price')
                            ->default('0')
                            ->numeric(),
                        TextInput::make('currency')
                            ->default('$'),
                        TextInput::make('period.en')
                            ->label('Period (EN)'),
                        TextInput::make('period.ar')
                            ->label('Period (AR)'),
                        TextInput::make('button_text.en')
                            ->label('Button Text (EN)')
                            ->default('Get Started'),
                        TextInput::make('button_text.ar')
                            ->label('Button Text (AR)')
                            ->default('ابدأ الآن'),
                        TextInput::make('button_url')
                            ->default('/contact-us'),
                        TextInput::make('sort_order')
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
