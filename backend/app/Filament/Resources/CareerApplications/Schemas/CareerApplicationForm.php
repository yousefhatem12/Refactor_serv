<?php

namespace App\Filament\Resources\CareerApplications\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;

class CareerApplicationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Applicant Details')
                    ->schema([
                        Select::make('career_id')
                            ->relationship('career', 'title')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->title['en'] ?? $record->title['ar'] ?? 'Job')
                            ->disabled()
                            ->required(),
                        TextInput::make('name')
                            ->disabled()
                            ->required(),
                        TextInput::make('email')
                            ->email()
                            ->disabled()
                            ->required(),
                        TextInput::make('phone')
                            ->disabled()
                            ->required(),
                        TextInput::make('portfolio')
                            ->label('Portfolio / CV Link')
                            ->disabled()
                            ->required(),
                        Textarea::make('message')
                            ->disabled(),
                    ]),

                Section::make('Application Status')
                    ->schema([
                        Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'reviewed' => 'Reviewed',
                                'accepted' => 'Accepted',
                                'rejected' => 'Rejected',
                            ])
                            ->required()
                            ->default('pending'),
                    ]),
            ]);
    }
}
