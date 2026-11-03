<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageBuilderResource\Pages;
use App\Models\Page;
use App\Models\AiGenerationRequest;
use App\Jobs\GenerateAiPageJob;
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
use Filament\Forms\Components\RichEditor\ToolbarButtonGroup;

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

    public static function getRichEditorToolbarButtons(): array
    {
        return [
            ['bold', 'italic', 'underline', 'strike', 'subscript', 'superscript', 'link'],
            [
                ToolbarButtonGroup::make('Format', [
                    'paragraph',
                    'h2',
                    'h3',
                    'h4',
                    'h5',
                    'h6',
                ])->textualButtons(),
            ],
            ['alignStart', 'alignCenter', 'alignEnd'],
            ['blockquote', 'codeBlock', 'bulletList', 'orderedList'],
            ['table', 'attachFiles'],
            ['undo', 'redo'],
        ];
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
                                Toggle::make('is_published')
                                    ->label('Published')
                                    ->default(true)
                                    ->disabled(fn (?Page $record): bool => $record?->ai_generated && in_array($record->ai_status, ['queued', 'generating', 'failed'], true))
                                    ->helperText('AI pages remain private while queued, generating, or failed.'),
                                TextInput::make('ai_status')
                                    ->label('AI Status')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->visible(fn (?Page $record): bool => (bool) $record?->ai_generated),
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
                                                Textarea::make('image_prompt')->label('AI Image Direction')->helperText('Use this direction when selecting or generating the background image.')->columnSpanFull(),
                                            ]),
                                        Block::make('text_block')
                                            ->label('Text Content')
                                            ->icon('heroicon-o-document-text')
                                            ->schema([
                                                 TextInput::make('heading.en')->label('Heading (EN)'),
                                                 TextInput::make('heading.ar')->label('Heading (AR)'),
                                                 \Filament\Forms\Components\RichEditor::make('content.en')->label('Content (EN)')->toolbarButtons(self::getRichEditorToolbarButtons())->columnSpanFull(),
                                                 \Filament\Forms\Components\RichEditor::make('content.ar')->label('Content (AR)')->toolbarButtons(self::getRichEditorToolbarButtons())->columnSpanFull(),
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
                                                        \Filament\Forms\Components\RichEditor::make('answer.en')->toolbarButtons(self::getRichEditorToolbarButtons())->label('Answer (EN)')->columnSpanFull(),
                                                        \Filament\Forms\Components\RichEditor::make('answer.ar')->toolbarButtons(self::getRichEditorToolbarButtons())->label('Answer (AR)')->columnSpanFull(),
                                                    ])->columns(2)
                                            ]),
                                        Block::make('split_block')
                                            ->label('Image & Text (Split)')
                                            ->icon('heroicon-o-view-columns')
                                            ->schema([
                                                Toggle::make('image_on_right')->label('Image on Right Side')->default(true),
                                                FileUpload::make('image')->label('Image')->image(),
                                                Textarea::make('image_prompt')->label('AI Image Direction')->helperText('Use this direction when selecting or generating the section image.')->columnSpanFull(),
                                                TextInput::make('title.en')->label('Title (EN)'),
                                                TextInput::make('title.ar')->label('Title (AR)'),
                                                \Filament\Forms\Components\RichEditor::make('content.en')->label('Content (EN)')->toolbarButtons(self::getRichEditorToolbarButtons())->columnSpanFull(),
                                                \Filament\Forms\Components\RichEditor::make('content.ar')->label('Content (AR)')->toolbarButtons(self::getRichEditorToolbarButtons())->columnSpanFull(),
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
                                Textarea::make('ai_metadata.visual_direction')
                                    ->label('AI Visual Direction')
                                    ->rows(4)
                                    ->visible(fn (?Page $record): bool => (bool) $record?->ai_generated),
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
                Tables\Columns\IconColumn::make('is_published')
                    ->label('Published')
                    ->boolean(),
                Tables\Columns\TextColumn::make('ai_status')
                    ->label('AI Status')
                    ->badge()
                    ->placeholder('Manual')
                    ->color(fn (?string $state): string => match ($state) {
                        'approved' => 'success',
                        'pending_review' => 'warning',
                        'queued', 'generating' => 'info',
                        'failed' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->actions([
                \Filament\Actions\Action::make('retryAi')
                    ->label('Retry AI')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->visible(fn (Page $record): bool => $record->ai_generated && $record->ai_status === 'failed')
                    ->requiresConfirmation()
                    ->action(function (Page $record): void {
                        $metadata = is_array($record->ai_metadata) ? $record->ai_metadata : [];
                        $record->update([
                            'ai_status' => 'queued',
                            'is_published' => false,
                            'ai_metadata' => array_merge($metadata, ['error' => null, 'queued_at' => now()->toIso8601String()]),
                        ]);
                        $requestId = $metadata['generation_request_id'] ?? null;
                        if ($requestId) {
                            AiGenerationRequest::whereKey($requestId)->update([
                                'status' => 'pending',
                                'completed_items' => 0,
                                'failed_items' => 0,
                                'error_message' => null,
                                'completed_at' => null,
                            ]);
                        }
                        GenerateAiPageJob::dispatch($record->id);
                    }),
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
