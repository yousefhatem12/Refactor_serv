<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectResource\Pages;
use App\Models\Project;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;
    protected static bool $shouldRegisterNavigation = false;
    public static function getNavigationGroup(): ?string { return __('Pages'); }
    protected static ?int $navigationSort = 3;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-folder-open';
    public static function getNavigationLabel(): string { return __('Our Work'); }

    public static function getModelLabel(): string
    {
        return __('Project');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Projects');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Project Details')
                    ->schema([
                        \Filament\Forms\Components\Hidden::make('type')
                            ->default('portfolio'),
                        TextInput::make('name.en')
                            ->label('Name (English)')->required(),
                        TextInput::make('name.ar')
                            ->label('Name (Arabic)')->required(),
                        \Filament\Forms\Components\Select::make('category')
                            ->options(fn () => \App\Models\ProjectCategory::all()->mapWithKeys(function ($cat) {
                                $name = $cat->name[app()->getLocale()] ?? $cat->name['en'] ?? $cat->slug;
                                return [$cat->slug => $name];
                            }))
                            ->required(),
                        Textarea::make('description.en')
                            ->label('Description (English)')->nullable(),
                        Textarea::make('description.ar')
                            ->label('Description (Arabic)')->nullable(),
                        FileUpload::make('image')
                            ->image()
                            ->directory('portfolio')
                            ->saveUploadedFileUsing(fn ($file) => \App\Services\ImageService::convertToWebp($file, 'portfolio')),
                        TextInput::make('client')
                            ->nullable(),
                        TextInput::make('url')
                            ->url()
                            ->nullable(),
                        \Filament\Forms\Components\TagsInput::make('tags')
                            ->label('Technologies/Tags')
                            ->placeholder('Add a tag...')
                            ->nullable(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image'),

                Tables\Columns\TextColumn::make('name.en')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('client')
                    ->searchable()
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }
}
