<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectConsultationResource\Pages;
use App\Models\ProjectConsultation;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProjectConsultationResource extends Resource
{
    protected static ?string $model = ProjectConsultation::class;
    protected static bool $shouldRegisterNavigation = false;
    public static function getNavigationGroup(): ?string { return __('Pages'); }
    protected static ?int $navigationSort = 9;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    public static function getModelLabel(): string
    {
        return __('Project Consultation');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Project Consultations');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Client Information')
                    ->schema([
                        TextInput::make('name')
                            ->disabled(),
                        TextInput::make('email')
                            ->disabled(),
                        TextInput::make('phone')
                            ->disabled(),
                        TextInput::make('company')
                            ->disabled(),
                    ])->columns(2),

                Section::make('Consultation Details')
                    ->schema([
                        TextInput::make('project_type')
                            ->disabled(),
                        TextInput::make('has_docs')
                            ->disabled(),
                        TextInput::make('budget')
                            ->disabled(),
                        TextInput::make('timeline')
                            ->disabled(),
                        TextInput::make('decision_maker')
                            ->disabled(),
                    ])->columns(2),

                Section::make('References & Notes')
                    ->schema([
                        Textarea::make('references')
                            ->disabled()
                            ->rows(5),
                    ])->columns(1),

                Section::make('Status')
                    ->schema([
                        Select::make('status')
                            ->options([
                                'unread' => 'Unread',
                                'read' => 'Read',
                            ])
                            ->default('unread'),
                        TextInput::make('read_at')
                            ->disabled(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('project_type')
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('budget')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'unread' => 'danger',
                        'read' => 'success',
                        default => 'secondary',
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
            'index' => Pages\ListProjectConsultations::route('/'),
            'view' => Pages\ViewProjectConsultation::route('/{record}'),
        ];
    }
}
