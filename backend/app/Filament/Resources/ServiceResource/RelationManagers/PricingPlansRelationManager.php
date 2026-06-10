<?php

namespace App\Filament\Resources\ServiceResource\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PricingPlansRelationManager extends RelationManager
{
    protected static string $relationship = 'pricingPlans';

    protected static ?string $title = 'Pricing Plans';

    private function getServiceName(): string
    {
        $record = $this->getOwnerRecord();
        return $record->name['en'] ?? $record->name['ar'] ?? 'Service';
    }

    /**
     * Shared form fields for Create & Edit modals.
     * service_id is omitted — Filament auto-assigns it via the relation.
     */
    private static function getFormSchema(): array
    {
        return [
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
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name.en')
                    ->label('Name')
                    ->searchable(),
                TextColumn::make('price')
                    ->sortable(),
                TextColumn::make('currency'),
                IconColumn::make('is_popular')
                    ->boolean(),
                IconColumn::make('is_active')
                    ->boolean(),
                TextColumn::make('button_url'),
                TextColumn::make('sort_order')
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
            ->headerActions([
                CreateAction::make()
                    ->label('New Pricing Plan')
                    ->modalHeading(fn () => 'Create Pricing Plan — ' . $this->getServiceName())
                    ->form(self::getFormSchema()),
            ])
            ->recordActions([
                EditAction::make()
                    ->modalHeading('Edit Pricing Plan')
                    ->form(self::getFormSchema()),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
