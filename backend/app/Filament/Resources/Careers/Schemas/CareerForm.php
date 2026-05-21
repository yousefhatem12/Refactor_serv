<?php

namespace App\Filament\Resources\Careers\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;

class CareerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('General Information')
                    ->schema([
                        TextInput::make('title.en')
                            ->label('Title (English)')
                            ->required(),
                        TextInput::make('title.ar')
                            ->label('Title (Arabic)')
                            ->required(),
                        Select::make('department')
                            ->options([
                                'Development' => 'Development',
                                'Design' => 'Design',
                                'Marketing' => 'Marketing',
                                'HR' => 'HR',
                            ])
                            ->required()
                            ->columnSpanFull(),
                        TextInput::make('location.en')
                            ->label('Location (English)')
                            ->required(),
                        TextInput::make('location.ar')
                            ->label('Location (Arabic)')
                            ->required(),
                        TextInput::make('salary.en')
                            ->label('Salary (English)')
                            ->required(),
                        TextInput::make('salary.ar')
                            ->label('Salary (Arabic)')
                            ->required(),
                        Textarea::make('description.en')
                            ->label('Description (English)')
                            ->required(),
                        Textarea::make('description.ar')
                            ->label('Description (Arabic)')
                            ->required(),
                        Toggle::make('is_active')
                            ->default(true)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('Responsibilities')
                    ->schema([
                        Repeater::make('responsibilities.en')
                            ->label('Responsibilities (English)')
                            ->schema([
                                TextInput::make('responsibility')
                                    ->required(),
                            ])
                            ->collapsible()
                            ->default([]),
                        Repeater::make('responsibilities.ar')
                            ->label('Responsibilities (Arabic)')
                            ->schema([
                                TextInput::make('responsibility')
                                    ->required(),
                            ])
                            ->collapsible()
                            ->default([]),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('Requirements')
                    ->schema([
                        Repeater::make('requirements.en')
                            ->label('Requirements (English)')
                            ->schema([
                                TextInput::make('requirement')
                                    ->required(),
                            ])
                            ->collapsible()
                            ->default([]),
                        Repeater::make('requirements.ar')
                            ->label('Requirements (Arabic)')
                            ->schema([
                                TextInput::make('requirement')
                                    ->required(),
                            ])
                            ->collapsible()
                            ->default([]),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
