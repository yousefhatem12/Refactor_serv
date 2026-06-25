<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TagsInput;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('General Information')
                    ->schema([
                        TextInput::make('slug')
                            ->required()
                            ->unique(table: 'products', column: 'slug', ignoreRecord: true),
                        TextInput::make('name.en')
                            ->label('Name (English)')
                            ->required(),
                        TextInput::make('name.ar')
                            ->label('Name (Arabic)')
                            ->required(),
                        TextInput::make('category.en')
                            ->label('Category (English)')
                            ->required(),
                        TextInput::make('category.ar')
                            ->label('Category (Arabic)')
                            ->required(),
                        TextInput::make('title.en')
                            ->label('Title (English)')
                            ->required(),
                        TextInput::make('title.ar')
                            ->label('Title (Arabic)')
                            ->required(),
                        Textarea::make('description.en')
                            ->label('Description (English)')
                            ->required(),
                        Textarea::make('description.ar')
                            ->label('Description (Arabic)')
                            ->required(),
                        Toggle::make('is_active')
                            ->default(true),
                    ]),

                Section::make('Media & Links')
                    ->schema([
                        FileUpload::make('image')
                            ->label('Desktop Image (صورة الديسكتوب)')
                            ->image()
                            ->directory('products')
                            ->saveUploadedFileUsing(fn ($file) => \App\Services\ImageService::convertToWebp($file, 'products')),
                        FileUpload::make('image_mobile')
                            ->label('Mobile Image (صورة الموبايل)')
                            ->image()
                            ->directory('products')
                            ->saveUploadedFileUsing(fn ($file) => \App\Services\ImageService::convertToWebp($file, 'products'))
                            ->nullable(),
                        TextInput::make('demo_link')
                            ->url()
                            ->nullable(),
                        TextInput::make('order_link')
                            ->url()
                            ->nullable(),
                    ]),

                Section::make('Product Details')
                    ->schema([
                        Textarea::make('why_choose_us.en')
                            ->label('Why Choose Us (English)'),
                        Textarea::make('why_choose_us.ar')
                            ->label('Why Choose Us (Arabic)'),
                        TagsInput::make('tech_specs')
                            ->label('Tech Specs/Technologies')
                            ->placeholder('Add technology...')
                            ->nullable(),
                    ]),

                Section::make('Features List')
                    ->schema([
                        Repeater::make('features.en')
                            ->label('Features (English)')
                            ->schema([
                                TextInput::make('title')->required(),
                                Textarea::make('desc')->required(),
                            ])
                            ->collapsible()
                            ->default([]),
                        Repeater::make('features.ar')
                            ->label('Features (Arabic)')
                            ->schema([
                                TextInput::make('title')->required(),
                                Textarea::make('desc')->required(),
                            ])
                            ->collapsible()
                            ->default([]),
                    ]),

                Section::make('Screenshots / Samples')
                    ->schema([
                        FileUpload::make('samples')
                            ->label('Screenshots')
                            ->multiple()
                            ->image()
                            ->directory('products/samples')
                            ->saveUploadedFileUsing(fn ($file) => \App\Services\ImageService::convertToWebp($file, 'products/samples')),
                    ]),
            ]);
    }
}
