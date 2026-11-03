<?php

namespace App\Filament\Resources\Settings\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SettingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->whereNotIn('key', [
                'hero_title_1', 'hero_title_2', 'hero_desc', 'hero_planet',
                'site_logo', 'site_title',
                'footer_heading', 'footer_description', 'footer_button_text', 'footer_copyright',
                'footer_heading_en', 'footer_heading_ar',
                'footer_description_en', 'footer_description_ar',
                'footer_button_text_en', 'footer_button_text_ar',
                'footer_copyright_en', 'footer_copyright_ar',
                'site_phone', 'site_email', 'site_address'
            ]))
            ->columns([
                TextColumn::make('key')
                    ->searchable(),
                TextColumn::make('type')
                    ->searchable(),
                TextColumn::make('value_source')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'link' => 'info',
                        'file' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
