<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Models\Post;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\User;
use Filament\Forms\Components\Select;
use Illuminate\Support\Str;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;
    protected static bool $shouldRegisterNavigation = false;
    public static function getNavigationGroup(): ?string { return __('Pages'); }
    protected static ?int $navigationSort = 4;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-newspaper';
    public static function getNavigationLabel(): string { return __('Blog'); }



    public static function getModelLabel(): string
    {
        return __('Post');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Posts');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Post Details')
                    ->schema([
                        TextInput::make('title.en')
                            ->label('Title (English)')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $operation, $state, $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),
                        TextInput::make('title.ar')
                            ->label('Title (Arabic)')
                            ->required(),
                        TextInput::make('slug')
                            ->required()
                            ->unique(Post::class, 'slug', ignoreRecord: true),
                        RichEditor::make('content.en')
                            ->label('Content (English)')
                            ->required()
                            ->columnSpanFull(),
                        RichEditor::make('content.ar')
                            ->label('Content (Arabic)')
                            ->required()
                            ->columnSpanFull(),
                        FileUpload::make('image')
                            ->image()
                            ->directory('blog')
                            ->saveUploadedFileUsing(fn ($file) => \App\Services\ImageService::convertToWebp($file, 'blog')),
                        Select::make('category')
                            ->options([
                                'TECHNOLOGY' => 'Technology',
                                'WEB DEVELOPMENT' => 'Web Development',
                                'STARTUP' => 'Startup',
                                'DROP SHIPPING' => 'Drop Shipping',
                                'HARDWARE' => 'Hardware',
                                'SOFTWARE' => 'Software',
                                'MARKETING' => 'Marketing',
                            ])
                            ->required()
                            ->searchable(),
                        Toggle::make('is_published')
                            ->default(false),
                        Toggle::make('is_trending')
                            ->label('Trending')
                            ->default(false),
                        Select::make('author_id')
                            ->relationship('author', 'name')
                            ->default(auth()->id())
                            ->required()
                            ->searchable(),
                    ]),
                Section::make('SEO Settings')
                    ->schema([
                        TextInput::make('seo_title.en')
                            ->label('SEO Title (EN)')
                            ->nullable(),
                        TextInput::make('seo_title.ar')
                            ->label('SEO Title (AR)')
                            ->nullable(),
                        \Filament\Forms\Components\Textarea::make('seo_description.en')
                            ->label('Meta Description (EN)')
                            ->nullable(),
                        \Filament\Forms\Components\Textarea::make('seo_description.ar')
                            ->label('Meta Description (AR)')
                            ->nullable(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image'),
                Tables\Columns\TextColumn::make('title.en')
                    ->label('Title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('author.name')
                    ->label('Author')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category')
                    ->badge()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_published')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_trending')
                    ->label('Trending')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\IconColumn::make('seo_description')
                    ->label('SEO')
                    ->getStateUsing(fn ($record) => !empty($record->seo_description['ar']) || !empty($record->seo_description['en']))
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
