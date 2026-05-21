<?php

namespace App\Filament\Resources\CareerApplications;

use App\Filament\Resources\CareerApplications\Pages\CreateCareerApplication;
use App\Filament\Resources\CareerApplications\Pages\EditCareerApplication;
use App\Filament\Resources\CareerApplications\Pages\ListCareerApplications;
use App\Filament\Resources\CareerApplications\Schemas\CareerApplicationForm;
use App\Filament\Resources\CareerApplications\Tables\CareerApplicationsTable;
use App\Models\CareerApplication;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class CareerApplicationResource extends Resource
{
    protected static ?string $model = CareerApplication::class;

    protected static bool $shouldRegisterNavigation = false;

    public static function getNavigationGroup(): ?string
    {
        return __('Pages');
    }

    public static function getNavigationLabel(): string
    {
        return __('Career Applications');
    }

    public static function getModelLabel(): string
    {
        return __('Career Application');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Career Applications');
    }

    public static function form(Schema $schema): Schema
    {
        return CareerApplicationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CareerApplicationsTable::configure($table);
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
            'index' => ListCareerApplications::route('/'),
            'create' => CreateCareerApplication::route('/create'),
            'edit' => EditCareerApplication::route('/{record}/edit'),
        ];
    }
}
