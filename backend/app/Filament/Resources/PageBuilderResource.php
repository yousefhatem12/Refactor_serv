<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageBuilderResource\Pages;
use App\Models\Page;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Group;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

class PageBuilderResource extends Resource
{
    protected static ?string $model = Page::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-duplicate';
    public static function getNavigationGroup(): ?string { return __('System Settings'); }
    protected static ?int $navigationSort = 50;
    
    public static function getNavigationLabel(): string
    {
        return __('Page Builder');
    }

    public static function getModelLabel(): string
    {
        return __('Dynamic Page');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Dynamic Pages');
    }

    public static function getEloquentQuery(): EloquentBuilder
    {
        return parent::getEloquentQuery()->whereNotIn('slug', ['home', 'about', 'faq']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make('Page Settings')
                            ->schema([
                                TextInput::make('title.en')
                                    ->label('Page Title (English)')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (string $operation, $state, $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),
                                TextInput::make('title.ar')
                                    ->label('Page Title (Arabic)')
                                    ->required(),
                                TextInput::make('slug')
                                    ->required()
                                    ->unique(Page::class, 'slug', ignoreRecord: true),
                                Select::make('sections.template')
                                    ->label('Page Template')
                                    ->options([
                                        'about' => 'About Us Template',
                                        'contact' => 'Contact Us Template',
                                        'portfolio' => 'Portfolio Template',
                                        'blog' => 'Blog Template',
                                        'faq' => 'FAQ Template',
                                        'custom' => 'Custom Builder (Blank)'
                                    ])
                                    ->default('custom')
                                    ->reactive()
                                    ->helperText('Choose a predefined template or build from scratch.'),
                            ])->columns(2),

                        Section::make('Page Content (Custom Builder)')
                            ->schema([
                                Builder::make('sections.blocks')
                                    ->label('Page Blocks')
                                    ->blocks([
                                        Block::make('hero')
                                            ->label('Hero Section')
                                            ->icon('heroicon-o-photo')
                                            ->schema([
                                                TextInput::make('title.en')->label('Title (EN)'),
                                                TextInput::make('title.ar')->label('Title (AR)'),
                                                Textarea::make('subtitle.en')->label('Subtitle (EN)'),
                                                Textarea::make('subtitle.ar')->label('Subtitle (AR)'),
                                                FileUpload::make('image')->label('Background Image')->image(),
                                            ]),
                                        Block::make('text_block')
                                            ->label('Text Content')
                                            ->icon('heroicon-o-document-text')
                                            ->schema([
                                                TextInput::make('heading.en')->label('Heading (EN)'),
                                                TextInput::make('heading.ar')->label('Heading (AR)'),
                                                \Filament\Forms\Components\RichEditor::make('content.en')->label('Content (EN)')->columnSpanFull(),
                                                \Filament\Forms\Components\RichEditor::make('content.ar')->label('Content (AR)')->columnSpanFull(),
                                            ]),
                                        Block::make('features_grid')
                                            ->label('Features/Services Grid')
                                            ->icon('heroicon-o-squares-2x2')
                                            ->schema([
                                                TextInput::make('section_title.en')->label('Section Title (EN)'),
                                                TextInput::make('section_title.ar')->label('Section Title (AR)'),
                                                \Filament\Forms\Components\Repeater::make('items')
                                                    ->schema([
                                                        TextInput::make('title.en')->label('Title (EN)'),
                                                        TextInput::make('title.ar')->label('Title (AR)'),
                                                        Textarea::make('desc.en')->label('Description (EN)'),
                                                        Textarea::make('desc.ar')->label('Description (AR)'),
                                                        TextInput::make('icon')->label('Icon (Heroicon)'),
                                                    ])->columns(2)
                                            ]),
                                        Block::make('cta_block')
                                            ->label('Call To Action (CTA)')
                                            ->icon('heroicon-o-megaphone')
                                            ->schema([
                                                TextInput::make('title.en')->label('Title (EN)'),
                                                TextInput::make('title.ar')->label('Title (AR)'),
                                                Textarea::make('subtitle.en')->label('Subtitle (EN)'),
                                                Textarea::make('subtitle.ar')->label('Subtitle (AR)'),
                                                TextInput::make('button_text.en')->label('Button Text (EN)'),
                                                TextInput::make('button_text.ar')->label('Button Text (AR)'),
                                                TextInput::make('button_link')->label('Button Link URL (e.g. /contact-us)'),
                                            ]),
                                        Block::make('faq_block')
                                            ->label('FAQ Section')
                                            ->icon('heroicon-o-question-mark-circle')
                                            ->schema([
                                                TextInput::make('section_title.en')->label('Section Title (EN)'),
                                                TextInput::make('section_title.ar')->label('Section Title (AR)'),
                                                \Filament\Forms\Components\Repeater::make('faqs')
                                                    ->label('Questions & Answers')
                                                    ->schema([
                                                        TextInput::make('question.en')->label('Question (EN)'),
                                                        TextInput::make('question.ar')->label('Question (AR)'),
                                                        \Filament\Forms\Components\RichEditor::make('answer.en')->label('Answer (EN)')->columnSpanFull(),
                                                        \Filament\Forms\Components\RichEditor::make('answer.ar')->label('Answer (AR)')->columnSpanFull(),
                                                    ])->columns(2)
                                            ]),
                                        Block::make('split_block')
                                            ->label('Image & Text (Split)')
                                            ->icon('heroicon-o-view-columns')
                                            ->schema([
                                                Toggle::make('image_on_right')->label('Image on Right Side')->default(true),
                                                FileUpload::make('image')->label('Image')->image(),
                                                TextInput::make('title.en')->label('Title (EN)'),
                                                TextInput::make('title.ar')->label('Title (AR)'),
                                                \Filament\Forms\Components\RichEditor::make('content.en')->label('Content (EN)')->columnSpanFull(),
                                                \Filament\Forms\Components\RichEditor::make('content.ar')->label('Content (AR)')->columnSpanFull(),
                                            ]),
                                        Block::make('stats_block')
                                            ->label('Statistics Grid')
                                            ->icon('heroicon-o-chart-bar')
                                            ->schema([
                                                \Filament\Forms\Components\Repeater::make('stats')
                                                    ->schema([
                                                        TextInput::make('number')->label('Number (e.g. 100+)'),
                                                        TextInput::make('label.en')->label('Label (EN)'),
                                                        TextInput::make('label.ar')->label('Label (AR)'),
                                                    ])->columns(3)
                                            ]),
                                    ])
                                    ->collapsible()
                            ])
                            ->visible(fn ($get) => $get('sections.template') === 'custom'),

                        Section::make('Template Data (For predefined templates)')
                            ->schema([
                                Textarea::make('subtitle.en')->label('Main Description (EN)'),
                                Textarea::make('subtitle.ar')->label('Main Description (AR)'),
                                FileUpload::make('hero_image')->label('Hero Image')->image(),
                            ])
                            ->visible(fn ($get) => in_array($get('sections.template'), ['about', 'services', 'contact', 'portfolio', 'blog', 'faq'])),

                    ])->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make('SEO Settings')
                            ->schema([
                                TextInput::make('seo_title.en')->label('SEO Title (EN)'),
                                TextInput::make('seo_title.ar')->label('SEO Title (AR)'),
                                Textarea::make('seo_description.en')->label('SEO Description (EN)'),
                                Textarea::make('seo_description.ar')->label('SEO Description (AR)'),
                            ]),
                    ])->columnSpan(['lg' => 1]),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title.en')
                    ->label('Title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sections.template')
                    ->label('Template')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}
