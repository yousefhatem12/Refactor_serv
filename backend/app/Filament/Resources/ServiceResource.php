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
    protected static bool $shouldRegisterNavigation = false;
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
                                Toggle::make('is_active')->default(true),
                            ])->columns(2),

                        \Filament\Schemas\Components\Tabs\Tab::make('Page Content')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                \Filament\Schemas\Components\Group::make()
                                    ->statePath('sections')
                                    ->schema([
                                        \Filament\Schemas\Components\Tabs::make('Content Tabs')
                                            ->tabs([
                                                \Filament\Schemas\Components\Tabs\Tab::make('Hero Section')
                                                    ->icon('heroicon-o-photo')
                                    ->schema([
                                        Toggle::make('show_hero')->label('Show Hero Section')->default(true),
                                        \Filament\Schemas\Components\Tabs::make('Hero Translations')
                                            ->tabs([
                                                \Filament\Schemas\Components\Tabs\Tab::make('English')
                                                    ->schema([
                                                        TextInput::make('hero_title.en')->label('Hero Title'),
                                                        Textarea::make('hero_subtitle.en')->label('Hero Subtitle'),
                                                    ]),
                                                \Filament\Schemas\Components\Tabs\Tab::make('Arabic')
                                                    ->schema([
                                                        TextInput::make('hero_title.ar')->label('Hero Title'),
                                                        Textarea::make('hero_subtitle.ar')->label('Hero Subtitle'),
                                                    ]),
                                            ])
                                    ]),

                                \Filament\Schemas\Components\Tabs\Tab::make('Features Section')
                                    ->icon('heroicon-o-star')
                                    ->schema([
                                        Toggle::make('show_features')->label('Show Features Section')->default(true),
                                        \Filament\Schemas\Components\Tabs::make('Features Translations')
                                            ->tabs([
                                                \Filament\Schemas\Components\Tabs\Tab::make('English')
                                                    ->schema([
                                                        TextInput::make('features_title.en')->label('Features Section Title'),
                                                        Textarea::make('features_subtitle.en')->label('Features Section Subtitle'),
                                                    ]),
                                                \Filament\Schemas\Components\Tabs\Tab::make('Arabic')
                                                    ->schema([
                                                        TextInput::make('features_title.ar')->label('Features Section Title'),
                                                        Textarea::make('features_subtitle.ar')->label('Features Section Subtitle'),
                                                    ]),
                                            ]),
                                        \Filament\Forms\Components\Repeater::make('features_list')
                                            ->label('Features Items')
                                            ->schema([
                                                TextInput::make('title.en')->label('Title (EN)'),
                                                TextInput::make('title.ar')->label('Title (AR)'),
                                                Textarea::make('desc.en')->label('Description (EN)'),
                                                Textarea::make('desc.ar')->label('Description (AR)'),
                                            ])->columns(2)
                                    ]),

                                \Filament\Schemas\Components\Tabs\Tab::make('How We Work Section')
                                    ->icon('heroicon-o-cog')
                                    ->schema([
                                        Toggle::make('show_how_we_work')->label('Show How We Work Section')->default(true),
                                        \Filament\Forms\Components\Repeater::make('how_we_work_list')
                                            ->label('Steps / Process Items')
                                            ->schema([
                                                TextInput::make('title.en')->label('Step Title (EN)'),
                                                TextInput::make('title.ar')->label('Step Title (AR)'),
                                                Textarea::make('desc.en')->label('Step Description (EN)'),
                                                Textarea::make('desc.ar')->label('Step Description (AR)'),
                                            ])->columns(2)
                                    ]),

                                \Filament\Schemas\Components\Tabs\Tab::make('Other Sections Visibility')
                                    ->icon('heroicon-o-eye')
                                    ->schema([
                                        Toggle::make('show_portfolio')->label('Show Portfolio Section')->default(true),
                                        Toggle::make('show_request')->label('Show Request Section')->default(true),
                                        Toggle::make('show_blogs')->label('Show Latest Blogs Section')->default(true),
                                        Toggle::make('show_hosting_offers')->label('Show Hosting Offers Section')->default(true),
                                        Toggle::make('show_digital_marketing')->label('Show Digital Marketing Services')->default(true),
                                        Toggle::make('show_why_choose_us')->label('Show Why Choose Us')->default(true),
                                    ])->columns(3),

                                \Filament\Schemas\Components\Tabs\Tab::make('Hosting Offers Section (Cards)')
                                    ->icon('heroicon-o-server')
                                    ->schema([
                                        \Filament\Forms\Components\Repeater::make('hosting_offers_list')
                                            ->label('Hosting Offers')
                                            ->schema([
                                                TextInput::make('title.en')->label('Offer Title (EN)'),
                                                TextInput::make('title.ar')->label('Offer Title (AR)'),
                                                Textarea::make('desc.en')->label('Offer Description (EN)'),
                                                Textarea::make('desc.ar')->label('Offer Description (AR)'),
                                            ])->columns(2)
                                    ]),

                                \Filament\Schemas\Components\Tabs\Tab::make('Digital Marketing Services (Cards)')
                                    ->icon('heroicon-o-presentation-chart-line')
                                    ->schema([
                                        \Filament\Schemas\Components\Tabs::make('Translations')
                                            ->tabs([
                                                \Filament\Schemas\Components\Tabs\Tab::make('English')
                                                    ->schema([
                                                        TextInput::make('digital_marketing_title.en')->label('Section Title'),
                                                        Textarea::make('digital_marketing_subtitle.en')->label('Section Subtitle'),
                                                    ]),
                                                \Filament\Schemas\Components\Tabs\Tab::make('Arabic')
                                                    ->schema([
                                                        TextInput::make('digital_marketing_title.ar')->label('Section Title'),
                                                        Textarea::make('digital_marketing_subtitle.ar')->label('Section Subtitle'),
                                                    ]),
                                            ]),
                                        \Filament\Forms\Components\Repeater::make('digital_marketing_list')
                                            ->label('Services Cards')
                                            ->schema([
                                                TextInput::make('title.en')->label('Card Title (EN)'),
                                                TextInput::make('title.ar')->label('Card Title (AR)'),
                                                Textarea::make('desc.en')->label('Card Description (EN)'),
                                                Textarea::make('desc.ar')->label('Card Description (AR)'),
                                            ])->columns(2)
                                    ]),

                                \Filament\Schemas\Components\Tabs\Tab::make('Why Choose Us Section')
                                    ->icon('heroicon-o-hand-thumb-up')
                                    ->schema([
                                        \Filament\Schemas\Components\Tabs::make('Translations')
                                            ->tabs([
                                                \Filament\Schemas\Components\Tabs\Tab::make('English')
                                                    ->schema([
                                                        TextInput::make('why_choose_us_title.en')->label('Section Title'),
                                                        Textarea::make('why_choose_us_subtitle.en')->label('Section Subtitle'),
                                                    ]),
                                                \Filament\Schemas\Components\Tabs\Tab::make('Arabic')
                                                    ->schema([
                                                        TextInput::make('why_choose_us_title.ar')->label('Section Title'),
                                                        Textarea::make('why_choose_us_subtitle.ar')->label('Section Subtitle'),
                                                    ]),
                                            ]),
                                        \Filament\Forms\Components\Repeater::make('why_choose_us_list')
                                            ->label('Why Choose Us Items')
                                            ->schema([
                                                TextInput::make('title.en')->label('Item Title (EN)'),
                                                TextInput::make('title.ar')->label('Item Title (AR)'),
                                                Textarea::make('desc.en')->label('Item Description (EN)'),
                                                Textarea::make('desc.ar')->label('Item Description (AR)'),
                                            ])->columns(2)
                                    ]),
                                            ])
                                    ])
                            ])
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
