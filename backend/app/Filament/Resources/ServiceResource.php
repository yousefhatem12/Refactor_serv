<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceResource\Pages;
use App\Models\Service;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-briefcase';
    protected static string|\UnitEnum|null $navigationGroup = 'Services';
    protected static ?int $navigationSort = 1;



    public static function getModelLabel(): string
    {
        return __('Service');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Services');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Service Details')
                    ->schema([
                        TextInput::make('name.en')
                            ->label('Name (English)')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $operation, $state, $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),
                        TextInput::make('name.ar')
                            ->label('Name (Arabic)')
                            ->required(),
                        TextInput::make('slug')
                            ->required()
                            ->unique(Service::class, 'slug', ignoreRecord: true),
                        Textarea::make('description.en')
                            ->label('Description (English)')
                            ->nullable(),
                        Textarea::make('description.ar')
                            ->label('Description (Arabic)')
                            ->nullable(),
                        TextInput::make('icon')
                            ->nullable()
                            ->helperText('Icon name from Heroicons or path to icon'),
                        Toggle::make('is_active')
                            ->default(true),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name.en')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\Resources\ServiceResource\RelationManagers\PricingPlansRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'edit' => Pages\EditService::route('/{record}/edit'),
        ];
    }
}
