<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuotationResource\Pages;
use App\Models\Quotation;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class QuotationResource extends Resource
{
    protected static ?string $model = Quotation::class;
    protected static bool $shouldRegisterNavigation = true;
    public static function getNavigationGroup(): ?string { return __('Leads'); }
    protected static ?int $navigationSort = 2;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    public static function getNavigationBadge(): ?string
    {
        return \Illuminate\Support\Facades\Cache::remember('badge_quotations', 60, fn () =>
            Quotation::where('status', 'unread')->count() ?: null
        );
    }

    public static function getNavigationBadgeColor(): ?string { return 'warning'; }

    public static function getModelLabel(): string
    {
        return __('Quotation');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Quotations');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Client Information')
                    ->schema([
                        TextInput::make('name')
                            ->disabled(),
                        TextInput::make('company')
                            ->disabled(),
                        TextInput::make('phone')
                            ->disabled(),
                        TextInput::make('email')
                            ->disabled(),
                        TextInput::make('country')
                            ->disabled(),
                        TextInput::make('city')
                            ->disabled(),
                    ])->columns(2),

                Section::make('Project Requirements')
                    ->schema([
                        TextInput::make('request_type')
                            ->disabled(),
                        TextInput::make('budget')
                            ->disabled(),
                        TextInput::make('website_link')
                            ->disabled(),
                        TextInput::make('content_managers')
                            ->disabled(),
                        TextInput::make('pre_designed_logo')
                            ->disabled(),
                        TextInput::make('design_images')
                            ->disabled(),
                    ])->columns(2),

                Section::make('Project Vision & Details')
                    ->schema([
                        Textarea::make('vision_goals')
                            ->disabled()->rows(3),
                        Textarea::make('product_brief')
                            ->disabled()->rows(3),
                        Textarea::make('color_preferences')
                            ->disabled()->rows(3),
                        Textarea::make('reference_sites')
                            ->disabled()->rows(3),
                        Textarea::make('additional_notes')
                            ->disabled()->rows(3),
                    ])->columns(1),

                Section::make('Attached Files')
                    ->schema([
                        TextEntry::make('files')
                            ->label('Download Attachments')
                            ->html()
                            ->state(function ($record): string {
                                $files = $record->files ?? [];
                                if (empty($files)) {
                                    return '<em class="text-gray-400">No files attached.</em>';
                                }
                                $links = array_map(function ($path) use ($record) {
                                    $filename = basename($path);
                                    $url = route('admin.quotation.file.download', [
                                        'quotation' => $record->id,
                                        'filename'  => $filename,
                                    ]);
                                    return "<a href=\"{$url}\" target=\"_blank\" class=\"text-primary-500 underline hover:no-underline\">{$filename}</a>";
                                }, $files);
                                return implode('<br>', $links);
                            }),
                    ])->columns(1),

                Section::make('Quotation Status')
                    ->schema([
                        Select::make('status')
                            ->options([
                                'unread' => 'Unread',
                                'read' => 'Read',
                                'in_progress' => 'In Progress',
                                'completed' => 'Completed',
                            ])
                            ->default('unread'),
                    ])->columns(1),
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
                Tables\Columns\TextColumn::make('request_type')
                    ->label('Type')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('budget')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'unread' => 'danger',
                        'read' => 'info',
                        'in_progress' => 'warning',
                        'completed' => 'success',
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
            'index' => Pages\ListQuotations::route('/'),
            'view' => Pages\ViewQuotation::route('/{record}'),
        ];
    }
}
