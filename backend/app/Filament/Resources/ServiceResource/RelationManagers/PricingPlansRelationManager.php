<?php

namespace App\Filament\Resources\ServiceResource\RelationManagers;

use App\Filament\Resources\PricingPlans\PricingPlanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class PricingPlansRelationManager extends RelationManager
{
    protected static string $relationship = 'pricingPlans';

    protected static ?string $relatedResource = PricingPlanResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}
