<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceResource\Pages;
use App\Models\Service;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;
    protected static bool $shouldRegisterNavigation = true;
    public static function getNavigationGroup(): ?string { return __('Pages'); }
    protected static ?int $navigationSort = 2;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-briefcase';
    public static function getNavigationLabel(): string { return __('Services'); }



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
                \Filament\Schemas\Components\Tabs::make('Service Settings')
                    ->tabs([
                        \Filament\Schemas\Components\Tabs\Tab::make('Basic Info')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                TextInput::make('name.en')->label('Name (English)')->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (string $operation, $state, $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),
                                TextInput::make('name.ar')->label('Name (Arabic)')->required(),
                                TextInput::make('slug')->required()->unique(Service::class, 'slug', ignoreRecord: true),
                                Textarea::make('description.en')->label('Description (English)')->nullable(),
                                Textarea::make('description.ar')->label('Description (Arabic)')->nullable(),
                                TextInput::make('icon')->nullable()->helperText('Icon name from Heroicons or path to icon'),
                                Select::make('parent_id')
                                    ->label('Parent Service')
                                    ->options(function ($record) {
                                        $query = Service::query()->where('is_active', true);
                                        if ($record) {
                                            $query->where('id', '!=', $record->id);
                                        }
                                        return $query->get()->mapWithKeys(function ($service) {
                                            $nameEn = is_array($service->name) ? ($service->name['en'] ?? '') : (json_decode($service->name, true)['en'] ?? $service->name);
                                            $nameAr = is_array($service->name) ? ($service->name['ar'] ?? '') : (json_decode($service->name, true)['ar'] ?? $service->name);
                                            return [$service->id => "{$nameEn} / {$nameAr}"];
                                        })->toArray();
                                    })
                                    ->nullable()
                                    ->searchable()
                                    ->placeholder('Select Parent Service (Optional)'),
                                Toggle::make('is_active')->default(true),
                            ])->columns(2),

                        \Filament\Schemas\Components\Tabs\Tab::make('Page Builder')
                            ->icon('heroicon-o-squares-2x2')
                            ->schema([
                                \Filament\Schemas\Components\Group::make()
                                    ->statePath('sections')
                                    ->schema([
                                        Select::make('template')
                                            ->label('Template Style')
                                            ->options(fn () => \App\Models\ServiceTemplate::pluck('name', 'slug')->toArray())
                                            ->required()
                                            ->live()
                                            ->afterStateUpdated(function ($state, $set, $get) {
                                                // Auto-fill blocks from template preset when template changes
                                                if ($state) {
                                                    $tmpl = \App\Models\ServiceTemplate::where('slug', $state)->first();
                                                    if ($tmpl && $tmpl->blocks_preset) {
                                                        $set('blocks', $tmpl->blocks_preset);
                                                    }
                                                }
                                            })
                                            ->helperText('Changing template will reset sections to the template defaults.')
                                            ->columnSpanFull(),

                                        Builder::make('blocks')
                                            ->label('Page Sections — Drag to reorder')
                                            ->blocks([
                                                // ────────── HERO ──────────
                                                Block::make('hero')
                                                    ->label('🎯 Hero Section')
                                                    ->icon('heroicon-o-photo')
                                                    ->schema([
                                                        TextInput::make('title.en')->label('Title (EN)'),
                                                        TextInput::make('title.ar')->label('Title (AR)'),
                                                        Textarea::make('subtitle.en')->label('Subtitle (EN)')->rows(3),
                                                        Textarea::make('subtitle.ar')->label('Subtitle (AR)')->rows(3),
                                                        FileUpload::make('hero_image')
                                                            ->label('Hero Background Image')
                                                            ->image()
                                                            ->directory('services/heroes')
                                                            ->helperText('Leave empty to use template default')
                                                            ->columnSpanFull(),
                                                    ])->columns(2),

                                                // ────────── FEATURES GRID ──────────
                                                Block::make('features')
                                                    ->label('⭐ Features Grid')
                                                    ->icon('heroicon-o-star')
                                                    ->schema([
                                                        TextInput::make('title.en')->label('Section Title (EN)'),
                                                        TextInput::make('title.ar')->label('Section Title (AR)'),
                                                        Textarea::make('subtitle.en')->label('Section Subtitle (EN)'),
                                                        Textarea::make('subtitle.ar')->label('Section Subtitle (AR)'),
                                                        Repeater::make('items')
                                                            ->label('Feature Items')
                                                            ->schema([
                                                                TextInput::make('title.en')->label('Title (EN)'),
                                                                TextInput::make('title.ar')->label('Title (AR)'),
                                                                Textarea::make('desc.en')->label('Description (EN)'),
                                                                Textarea::make('desc.ar')->label('Description (AR)'),
                                                            ])->columns(2)->columnSpanFull()->collapsible(),
                                                    ])->columns(2),

                                                // ────────── HOW WE WORK (Timeline) ──────────
                                                Block::make('how_we_work_timeline')
                                                    ->label('🔄 How We Work (Timeline)')
                                                    ->icon('heroicon-o-arrow-path')
                                                    ->schema([
                                                        Repeater::make('items')
                                                            ->label('Steps')
                                                            ->schema([
                                                                TextInput::make('title.en')->label('Step Title (EN)'),
                                                                TextInput::make('title.ar')->label('Step Title (AR)'),
                                                                Textarea::make('desc.en')->label('Step Description (EN)'),
                                                                Textarea::make('desc.ar')->label('Step Description (AR)'),
                                                            ])->columns(2)->collapsible(),
                                                    ]),

                                                // ────────── HOW WE WORK (Pipeline Steps) ──────────
                                                Block::make('how_we_work_steps')
                                                    ->label('📋 How We Work (Pipeline)')
                                                    ->icon('heroicon-o-queue-list')
                                                    ->schema([
                                                        Repeater::make('items')
                                                            ->label('Steps')
                                                            ->schema([
                                                                TextInput::make('title.en')->label('Step Title (EN)'),
                                                                TextInput::make('title.ar')->label('Step Title (AR)'),
                                                                Textarea::make('desc.en')->label('Step Description (EN)'),
                                                                Textarea::make('desc.ar')->label('Step Description (AR)'),
                                                            ])->columns(2)->collapsible(),
                                                    ]),

                                                // ────────── HOW WE WORK (Mobile) ──────────
                                                Block::make('how_we_work_mobile')
                                                    ->label('📱 How We Work (Mobile Style)')
                                                    ->icon('heroicon-o-device-phone-mobile')
                                                    ->schema([
                                                        Repeater::make('items')
                                                            ->label('Steps')
                                                            ->schema([
                                                                TextInput::make('title.en')->label('Step Title (EN)'),
                                                                TextInput::make('title.ar')->label('Step Title (AR)'),
                                                                Textarea::make('desc.en')->label('Step Description (EN)'),
                                                                Textarea::make('desc.ar')->label('Step Description (AR)'),
                                                            ])->columns(2)->collapsible(),
                                                    ]),

                                                // ────────── DIGITAL MARKETING SERVICES ──────────
                                                Block::make('digital_marketing')
                                                    ->label('📢 Digital Marketing Services')
                                                    ->icon('heroicon-o-presentation-chart-line')
                                                    ->schema([
                                                        TextInput::make('title.en')->label('Section Title (EN)'),
                                                        TextInput::make('title.ar')->label('Section Title (AR)'),
                                                        TextInput::make('subtitle.en')->label('Section Subtitle (EN)'),
                                                        TextInput::make('subtitle.ar')->label('Section Subtitle (AR)'),
                                                        Repeater::make('items')
                                                            ->label('Service Cards')
                                                            ->schema([
                                                                TextInput::make('title.en')->label('Card Title (EN)'),
                                                                TextInput::make('title.ar')->label('Card Title (AR)'),
                                                                Textarea::make('desc.en')->label('Card Description (EN)'),
                                                                Textarea::make('desc.ar')->label('Card Description (AR)'),
                                                            ])->columns(2)->columnSpanFull()->collapsible(),
                                                    ])->columns(2),

                                                // ────────── WHY CHOOSE US ──────────
                                                Block::make('why_choose_us')
                                                    ->label('👍 Why Choose Us')
                                                    ->icon('heroicon-o-hand-thumb-up')
                                                    ->schema([
                                                        TextInput::make('title.en')->label('Section Title (EN)'),
                                                        TextInput::make('title.ar')->label('Section Title (AR)'),
                                                        TextInput::make('subtitle.en')->label('Section Subtitle (EN)'),
                                                        TextInput::make('subtitle.ar')->label('Section Subtitle (AR)'),
                                                        Repeater::make('items')
                                                            ->label('Reasons / Benefits')
                                                            ->schema([
                                                                TextInput::make('title.en')->label('Title (EN)'),
                                                                TextInput::make('title.ar')->label('Title (AR)'),
                                                                Textarea::make('desc.en')->label('Description (EN)'),
                                                                Textarea::make('desc.ar')->label('Description (AR)'),
                                                            ])->columns(2)->columnSpanFull()->collapsible(),
                                                    ])->columns(2),

                                                // ────────── PORTFOLIO ──────────
                                                Block::make('portfolio')
                                                    ->label('🎨 Portfolio Gallery')
                                                    ->icon('heroicon-o-photo')
                                                    ->schema([
                                                        TextInput::make('section_title.en')->label('Section Title (EN)'),
                                                        TextInput::make('section_title.ar')->label('Section Title (AR)'),
                                                        Toggle::make('is_mobile_app')->label('Show as Mobile App Portfolio')->default(false),
                                                    ])->columns(2),

                                                // ────────── CONTACT / REQUEST ──────────
                                                Block::make('contact_request')
                                                    ->label('📧 Contact / Request Form')
                                                    ->icon('heroicon-o-envelope')
                                                    ->schema([
                                                        Select::make('style')
                                                            ->label('Form Style')
                                                            ->options([
                                                                'social_media' => 'Social Media Contact Style',
                                                                'web_design' => 'Web Design Request Style',
                                                            ])
                                                            ->default('social_media'),
                                                    ]),

                                                Block::make('blogs')
                                                    ->label('📰 Latest Blogs')
                                                    ->icon('heroicon-o-newspaper')
                                                    ->schema([
                                                        TextInput::make('info')
                                                            ->hiddenLabel()
                                                            ->default('⚡ Auto-fetches latest blog posts. No configuration needed.')
                                                            ->disabled()
                                                            ->dehydrated(false)
                                                            ->columnSpanFull(),
                                                    ]),

                                                Block::make('ecommerce_features')
                                                    ->label('🛒 E-commerce Features')
                                                    ->icon('heroicon-o-shopping-cart')
                                                    ->schema([
                                                        TextInput::make('title.en')->label('Section Title (EN)'),
                                                        TextInput::make('title.ar')->label('Section Title (AR)'),
                                                        Textarea::make('subtitle.en')->label('Section Subtitle (EN)'),
                                                        Textarea::make('subtitle.ar')->label('Section Subtitle (AR)'),
                                                        Repeater::make('items')
                                                            ->label('Feature Items')
                                                            ->schema([
                                                                TextInput::make('title.en')->label('Title (EN)'),
                                                                TextInput::make('title.ar')->label('Title (AR)'),
                                                                Textarea::make('desc.en')->label('Description (EN)'),
                                                                Textarea::make('desc.ar')->label('Description (AR)'),
                                                            ])->columns(2)->columnSpanFull()->collapsible(),
                                                    ])->columns(2),

                                                // ────────── SPLIT CONTENT (Image + Text) ──────────
                                                Block::make('split_content')
                                                    ->label('📐 Split Content Block')
                                                    ->icon('heroicon-o-view-columns')
                                                    ->schema([
                                                        TextInput::make('title.en')->label('Title (EN)'),
                                                        TextInput::make('title.ar')->label('Title (AR)'),
                                                        Textarea::make('content.en')->label('Content Text (EN)')->rows(4),
                                                        Textarea::make('content.ar')->label('Content Text (AR)')->rows(4),
                                                        Toggle::make('image_on_right')->label('Image on Right Side')->default(true),
                                                        FileUpload::make('image')
                                                            ->label('Section Image')
                                                            ->image()
                                                            ->directory('services/content'),
                                                        Repeater::make('items')
                                                            ->label('Bullet Points (optional)')
                                                            ->schema([
                                                                TextInput::make('title.en')->label('Point (EN)'),
                                                                TextInput::make('title.ar')->label('Point (AR)'),
                                                            ])->columns(2)->collapsible()->columnSpanFull(),
                                                    ])->columns(2),

                                                // ────────── PRICING PLANS ──────────
                                                Block::make('hosting_offers')
                                                    ->label('💰 Pricing Plans')
                                                    ->icon('heroicon-o-credit-card')
                                                    ->schema([
                                                        TextInput::make('title.en')->label('Section Title (EN)'),
                                                        TextInput::make('title.ar')->label('Section Title (AR)'),
                                                        Repeater::make('items')
                                                            ->label('Pricing Plans')
                                                            ->schema([
                                                                TextInput::make('title.en')->label('Plan Title (EN)'),
                                                                TextInput::make('title.ar')->label('Plan Title (AR)'),
                                                                Textarea::make('desc.en')->label('Plan Description (EN)'),
                                                                Textarea::make('desc.ar')->label('Plan Description (AR)'),
                                                            ])->columns(2)->collapsible(),
                                                    ]),
                                            ])
                                            ->collapsible()
                                            ->collapsed(false)
                                            ->blockNumbers(false)
                                            ->reorderableWithButtons()
                                            ->addActionLabel('➕ Add Section')
                                            ->columnSpanFull(),
                                    ])
                            ]),
                    ])->columnSpanFull()
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
                Tables\Columns\TextColumn::make('parent.name')
                    ->label('Parent Service')
                    ->formatStateUsing(fn ($state) => is_array($state) ? ($state['en'] ?? '') : (json_decode($state, true)['en'] ?? $state))
                    ->placeholder('None')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sections.template')
                    ->label('Template')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'template-one' => 'info',
                        'template-two' => 'success',
                        'template-three' => 'warning',
                        default => 'gray',
                    }),
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
            //
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
