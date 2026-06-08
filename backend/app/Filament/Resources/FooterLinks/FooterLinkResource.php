<?php

namespace App\Filament\Resources\FooterLinks;

use App\Filament\Resources\FooterLinks\Pages\CreateFooterLink;
use App\Filament\Resources\FooterLinks\Pages\EditFooterLink;
use App\Filament\Resources\FooterLinks\Pages\ListFooterLinks;
use App\Filament\Resources\FooterLinks\Schemas\FooterLinkForm;
use App\Filament\Resources\FooterLinks\Tables\FooterLinksTable;
use App\Models\FooterLink;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class FooterLinkResource extends Resource
{
    protected static ?string $model = FooterLink::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-link';
    public static function getNavigationGroup(): ?string { return __('Footer'); }
    protected static ?int $navigationSort = 2;
    public static function getNavigationLabel(): string { return __('Footer Links'); }

    public static function getRecordTitle(?\Illuminate\Database\Eloquent\Model $record): ?string
    {
        /** @var FooterLink|null $record */
        if (is_array($record?->label)) {
            return $record->label['en'] ?? $record->label['ar'] ?? null;
        }

        return $record?->label;
    }

    public static function form(Schema $schema): Schema
    {
        return FooterLinkForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FooterLinksTable::configure($table);
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
            'index' => ListFooterLinks::route('/'),
            'create' => CreateFooterLink::route('/create'),
            'edit' => EditFooterLink::route('/{record}/edit'),
        ];
    }
}
