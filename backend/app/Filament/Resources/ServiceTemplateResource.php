<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceTemplateResource\Pages;
use App\Models\ServiceTemplate;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ServiceTemplateResource extends Resource
{
    protected static ?string $model = ServiceTemplate::class;
    public static function getNavigationGroup(): ?string { return __('Pages'); }
    protected static ?int $navigationSort = 8;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-squares-plus';
    public static function getNavigationLabel(): string { return __('Service Templates'); }

    public static function getModelLabel(): string { return __('Template'); }
    public static function getPluralModelLabel(): string { return __('Templates'); }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('Template Settings')
                    ->schema([
                        TextInput::make('name')
                            ->label('Template Name')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $operation, $state, $set) =>
                                $operation === 'create' ? $set('slug', Str::slug($state)) : null
                            ),
                        TextInput::make('slug')
                            ->required()
                            ->unique(ServiceTemplate::class, 'slug', ignoreRecord: true),
                        Select::make('hero_style')
                            ->label('Hero Component Style')
                            ->options([
                                'social_media_hero' => 'Social Media Hero (Full bg image, no planet)',
                                'web_dev_hero' => 'Web Dev Hero (Space theme with overlay)',
                            ])
                            ->default('social_media_hero')
                            ->required()
                            ->helperText('Determines which Hero component renders on the frontend'),
                        Textarea::make('description')
                            ->label('Description (for admin reference)')
                            ->nullable(),
                        FileUpload::make('default_hero_image')
                            ->label('Default Hero Background Image')
                            ->image()
                            ->directory('templates/heroes')
                            ->helperText('Default hero background for services using this template.'),
                    ])->columns(2),

                \Filament\Schemas\Components\Section::make('Default Sections (Preset)')
                    ->description('Define which sections this template includes by default. When a service picks this template, these sections auto-fill the page builder.')
                    ->schema([
                        \Filament\Forms\Components\Builder::make('blocks_preset')
                            ->label('Template Sections')
                            ->blocks([
                                \Filament\Forms\Components\Builder\Block::make('hero')
                                    ->label('🎯 Hero Section')->icon('heroicon-o-photo')
                                    ->schema([
                                        TextInput::make('title.en')->label('Title (EN)'),
                                        TextInput::make('title.ar')->label('Title (AR)'),
                                        Textarea::make('subtitle.en')->label('Subtitle (EN)')->rows(3),
                                        Textarea::make('subtitle.ar')->label('Subtitle (AR)')->rows(3),
                                    ])->columns(2),
                                \Filament\Forms\Components\Builder\Block::make('features')
                                    ->label('⭐ Features Grid')->icon('heroicon-o-star')
                                    ->schema([
                                        TextInput::make('title.en')->label('Section Title (EN)'),
                                        TextInput::make('title.ar')->label('Section Title (AR)'),
                                        Textarea::make('subtitle.en')->label('Section Subtitle (EN)'),
                                        Textarea::make('subtitle.ar')->label('Section Subtitle (AR)'),
                                        \Filament\Forms\Components\Repeater::make('items')
                                            ->label('Feature Items')
                                            ->schema([
                                                TextInput::make('title.en')->label('Title (EN)'),
                                                TextInput::make('title.ar')->label('Title (AR)'),
                                                Textarea::make('desc.en')->label('Description (EN)'),
                                                Textarea::make('desc.ar')->label('Description (AR)'),
                                            ])->columns(2)->columnSpanFull()->collapsible(),
                                    ])->columns(2),
                                \Filament\Forms\Components\Builder\Block::make('how_we_work_timeline')
                                    ->label('🔄 How We Work (Timeline)')->icon('heroicon-o-arrow-path')
                                    ->schema([
                                        \Filament\Forms\Components\Repeater::make('items')
                                            ->label('Steps')
                                            ->schema([
                                                TextInput::make('title.en')->label('Step Title (EN)'),
                                                TextInput::make('title.ar')->label('Step Title (AR)'),
                                                Textarea::make('desc.en')->label('Step Description (EN)'),
                                                Textarea::make('desc.ar')->label('Step Description (AR)'),
                                            ])->columns(2)->collapsible(),
                                    ]),
                                \Filament\Forms\Components\Builder\Block::make('how_we_work_steps')
                                    ->label('📋 How We Work (Pipeline)')->icon('heroicon-o-queue-list')
                                    ->schema([
                                        \Filament\Forms\Components\Repeater::make('items')
                                            ->label('Steps')
                                            ->schema([
                                                TextInput::make('title.en')->label('Step Title (EN)'),
                                                TextInput::make('title.ar')->label('Step Title (AR)'),
                                                Textarea::make('desc.en')->label('Step Description (EN)'),
                                                Textarea::make('desc.ar')->label('Step Description (AR)'),
                                            ])->columns(2)->collapsible(),
                                    ]),
                                \Filament\Forms\Components\Builder\Block::make('how_we_work_mobile')
                                    ->label('📱 How We Work (Mobile)')->icon('heroicon-o-device-phone-mobile')
                                    ->schema([
                                        \Filament\Forms\Components\Repeater::make('items')
                                            ->label('Steps')
                                            ->schema([
                                                TextInput::make('title.en')->label('Step Title (EN)'),
                                                TextInput::make('title.ar')->label('Step Title (AR)'),
                                                Textarea::make('desc.en')->label('Step Description (EN)'),
                                                Textarea::make('desc.ar')->label('Step Description (AR)'),
                                            ])->columns(2)->collapsible(),
                                    ]),
                                \Filament\Forms\Components\Builder\Block::make('digital_marketing')
                                    ->label('📢 Digital Marketing Services')->icon('heroicon-o-presentation-chart-line')
                                    ->schema([
                                        TextInput::make('title.en')->label('Section Title (EN)'),
                                        TextInput::make('title.ar')->label('Section Title (AR)'),
                                        TextInput::make('subtitle.en')->label('Section Subtitle (EN)'),
                                        TextInput::make('subtitle.ar')->label('Section Subtitle (AR)'),
                                        \Filament\Forms\Components\Repeater::make('items')
                                            ->label('Service Cards')
                                            ->schema([
                                                TextInput::make('title.en')->label('Card Title (EN)'),
                                                TextInput::make('title.ar')->label('Card Title (AR)'),
                                                Textarea::make('desc.en')->label('Card Description (EN)'),
                                                Textarea::make('desc.ar')->label('Card Description (AR)'),
                                            ])->columns(2)->columnSpanFull()->collapsible(),
                                    ])->columns(2),
                                \Filament\Forms\Components\Builder\Block::make('why_choose_us')
                                    ->label('👍 Why Choose Us')->icon('heroicon-o-hand-thumb-up')
                                    ->schema([
                                        TextInput::make('title.en')->label('Section Title (EN)'),
                                        TextInput::make('title.ar')->label('Section Title (AR)'),
                                        TextInput::make('subtitle.en')->label('Section Subtitle (EN)'),
                                        TextInput::make('subtitle.ar')->label('Section Subtitle (AR)'),
                                        \Filament\Forms\Components\Repeater::make('items')
                                            ->label('Reasons / Benefits')
                                            ->schema([
                                                TextInput::make('title.en')->label('Title (EN)'),
                                                TextInput::make('title.ar')->label('Title (AR)'),
                                                Textarea::make('desc.en')->label('Description (EN)'),
                                                Textarea::make('desc.ar')->label('Description (AR)'),
                                            ])->columns(2)->columnSpanFull()->collapsible(),
                                    ])->columns(2),
                                \Filament\Forms\Components\Builder\Block::make('portfolio')
                                    ->label('🎨 Portfolio Gallery')->icon('heroicon-o-photo')
                                    ->schema([
                                        TextInput::make('section_title.en')->label('Section Title (EN)'),
                                        TextInput::make('section_title.ar')->label('Section Title (AR)'),
                                        \Filament\Forms\Components\Toggle::make('is_mobile_app')->label('Show as Mobile App Portfolio')->default(false),
                                    ])->columns(2),
                                \Filament\Forms\Components\Builder\Block::make('contact_request')
                                    ->label('📧 Contact / Request Form')->icon('heroicon-o-envelope')
                                    ->schema([
                                        Select::make('style')
                                            ->label('Form Style')
                                            ->options(['social_media' => 'Social Media Style', 'web_design' => 'Web Design Style'])
                                            ->default('social_media'),
                                    ]),
                                \Filament\Forms\Components\Builder\Block::make('hosting_offers')
                                    ->label('💰 Hosting / Pricing Offers')->icon('heroicon-o-server')
                                    ->schema([
                                        TextInput::make('title.en')->label('Section Title (EN)'),
                                        TextInput::make('title.ar')->label('Section Title (AR)'),
                                        \Filament\Forms\Components\Repeater::make('items')
                                            ->label('Offer Cards')
                                            ->schema([
                                                TextInput::make('title.en')->label('Offer Title (EN)'),
                                                TextInput::make('title.ar')->label('Offer Title (AR)'),
                                                Textarea::make('desc.en')->label('Offer Description (EN)'),
                                                Textarea::make('desc.ar')->label('Offer Description (AR)'),
                                            ])->columns(2)->collapsible(),
                                    ]),
                                \Filament\Forms\Components\Builder\Block::make('blogs')
                                    ->label('📰 Latest Blogs')->icon('heroicon-o-newspaper')
                                    ->schema([
                                        \Filament\Infolists\Components\TextEntry::make('info')
                                            ->state('⚡ This section auto-fetches the latest blog posts. No configuration needed.')
                                            ->columnSpanFull(),
                                    ]),
                                \Filament\Forms\Components\Builder\Block::make('ecommerce_features')
                                    ->label('🛒 E-commerce Features')->icon('heroicon-o-shopping-cart')
                                    ->schema([
                                        \Filament\Infolists\Components\TextEntry::make('info')
                                            ->state('⚡ This section uses the default e-commerce features component. No configuration needed.')
                                            ->columnSpanFull(),
                                    ]),
                                \Filament\Forms\Components\Builder\Block::make('split_content')
                                    ->label('📐 Split Content Block')->icon('heroicon-o-view-columns')
                                    ->schema([
                                        TextInput::make('title.en')->label('Title (EN)'),
                                        TextInput::make('title.ar')->label('Title (AR)'),
                                        Textarea::make('content.en')->label('Content Text (EN)')->rows(4),
                                        Textarea::make('content.ar')->label('Content Text (AR)')->rows(4),
                                        \Filament\Forms\Components\Toggle::make('image_on_right')->label('Image on Right Side')->default(true),
                                        \Filament\Forms\Components\Repeater::make('items')
                                            ->label('Bullet Points (optional)')
                                            ->schema([
                                                TextInput::make('title.en')->label('Point (EN)'),
                                                TextInput::make('title.ar')->label('Point (AR)'),
                                            ])->columns(2)->collapsible()->columnSpanFull(),
                                    ])->columns(2),
                            ])

                            ->collapsible()
                            ->collapsed(false)
                            ->blockNumbers(false)
                            ->reorderableWithButtons()
                            ->addActionLabel('➕ Add Section')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable()
                    ->badge(),
                Tables\Columns\TextColumn::make('hero_style')
                    ->label('Hero Style')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'social_media_hero' => 'info',
                        'web_dev_hero' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('description')
                    ->limit(50),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServiceTemplates::route('/'),
            'create' => Pages\CreateServiceTemplate::route('/create'),
            'edit' => Pages\EditServiceTemplate::route('/{record}/edit'),
        ];
    }
}
