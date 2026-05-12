<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactMessageResource\Pages;
use App\Models\ContactMessage;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ContactMessageResource extends Resource
{
    protected static ?string $model = ContactMessage::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';
    protected static string|\UnitEnum|null $navigationGroup = 'About Us';
    protected static ?int $navigationSort = 1;



    public static function getModelLabel(): string
    {
        return __('Contact Message');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Contact Messages');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Message Details')
                    ->schema([
                        TextInput::make('name')
                            ->disabled(),
                        TextInput::make('email')
                            ->disabled(),
                        TextInput::make('phone')
                            ->disabled(),
                        TextInput::make('type')
                            ->disabled(),
                        Textarea::make('message')
                            ->disabled(),
                        Select::make('status')
                            ->options([
                                'unread' => 'Unread',
                                'read' => 'Read',
                            ])
                            ->default('unread'),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'inquiry' => 'info',
                        'pricing' => 'success',
                        'complaint' => 'danger',
                        'support' => 'warning',
                        'job' => 'primary',
                        default => 'secondary',
                    })
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'unread' => 'danger',
                        'read' => 'success',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                \Filament\Actions\ViewAction::make(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContactMessages::route('/'),
            'view' => Pages\ViewContactMessage::route('/{record}'),
        ];
    }
}
