<?php

namespace App\Filament\Resources\Pages\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class PageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true),
                \Filament\Schemas\Components\Tabs::make('Translations')
                    ->tabs([
                        \Filament\Schemas\Components\Tabs\Tab::make('English')
                            ->schema([
                                TextInput::make('title.en')->label('Title'),
                                TextInput::make('subtitle.en')->label('Subtitle'),
                                Textarea::make('content.en')->label('Content'),
                                TextInput::make('seo_title.en')->label('SEO Title'),
                                Textarea::make('seo_description.en')->label('SEO Description'),
                                \Filament\Forms\Components\Builder::make('sections.en')
                                    ->label('Page Sections')
                                    ->blocks([
                                        \Filament\Forms\Components\Builder\Block::make('text_image')
                                            ->label('Text & Image Section')
                                            ->schema([
                                                TextInput::make('title')->required(),
                                                TextInput::make('tagline'),
                                                Textarea::make('content')->required(),
                                                \Filament\Forms\Components\FileUpload::make('image')
                                                    ->image()
                                                    ->directory('pages')
                                                    ->saveUploadedFileUsing(fn ($file) => \App\Services\ImageService::convertToWebp($file, 'pages')),
                                                \Filament\Forms\Components\Select::make('layout')
                                                    ->options([
                                                        'text_left' => 'Text Left, Image Right',
                                                        'text_right' => 'Text Right, Image Left',
                                                    ])->default('text_left'),
                                            ]),
                                        \Filament\Forms\Components\Builder\Block::make('stats')
                                            ->label('Stats Section')
                                            ->schema([
                                                TextInput::make('title'),
                                                \Filament\Forms\Components\Repeater::make('items')
                                                    ->schema([
                                                        TextInput::make('label')->required(),
                                                        TextInput::make('value')->required(),
                                                        TextInput::make('icon')->label('Icon Name (e.g., heroicon-o-clock)'),
                                                    ])->columns(2),
                                            ]),
                                        \Filament\Forms\Components\Builder\Block::make('features')
                                            ->label('Features Section')
                                            ->schema([
                                                TextInput::make('title'),
                                                \Filament\Forms\Components\Repeater::make('items')
                                                    ->schema([
                                                        TextInput::make('title')->required(),
                                                        Textarea::make('description')->required(),
                                                        TextInput::make('icon')->label('Icon Name (e.g., FaRocket)'),
                                                    ])->columns(1),
                                            ]),
                                        \Filament\Forms\Components\Builder\Block::make('journey')
                                            ->label('Journey / Timeline Section')
                                            ->schema([
                                                TextInput::make('title'),
                                                Textarea::make('description'),
                                                \Filament\Forms\Components\Repeater::make('items')
                                                    ->schema([
                                                        TextInput::make('year')->required(),
                                                        \Filament\Forms\Components\FileUpload::make('planet')->label('Planet Image')->image()
                                                            ->directory('pages')
                                                            ->saveUploadedFileUsing(fn ($file) => \App\Services\ImageService::convertToWebp($file, 'pages')),
                                                        TextInput::make('location')->required(),
                                                        TextInput::make('users')->required(),
                                                        TextInput::make('partnership'),
                                                        Textarea::make('description')->required(),
                                                    ])->columns(2),
                                            ]),
                                        \Filament\Forms\Components\Builder\Block::make('team')
                                            ->label('Team / Founders Section')
                                            ->schema([
                                                TextInput::make('title'),
                                                Textarea::make('description'),
                                                \Filament\Forms\Components\Repeater::make('items')
                                                    ->schema([
                                                        TextInput::make('name')->required(),
                                                        TextInput::make('role')->required(),
                                                        \Filament\Forms\Components\FileUpload::make('image')->image()->required()
                                                            ->directory('pages')
                                                            ->saveUploadedFileUsing(fn ($file) => \App\Services\ImageService::convertToWebp($file, 'pages')),
                                                    ])->columns(2),
                                            ]),
                                        \Filament\Forms\Components\Builder\Block::make('cta')
                                            ->label('Call To Action Section')
                                            ->schema([
                                                TextInput::make('title')->required(),
                                                Textarea::make('description'),
                                                TextInput::make('button_text'),
                                                TextInput::make('button_url'),
                                            ]),
                                    ]),
                            ]),
                        \Filament\Schemas\Components\Tabs\Tab::make('Arabic')
                            ->schema([
                                TextInput::make('title.ar')->label('Title'),
                                TextInput::make('subtitle.ar')->label('Subtitle'),
                                Textarea::make('content.ar')->label('Content'),
                                TextInput::make('seo_title.ar')->label('SEO Title'),
                                Textarea::make('seo_description.ar')->label('SEO Description'),
                                \Filament\Forms\Components\Builder::make('sections.ar')
                                    ->label('Page Sections (Arabic)')
                                    ->blocks([
                                        \Filament\Forms\Components\Builder\Block::make('text_image')
                                            ->label('Text & Image Section')
                                            ->schema([
                                                TextInput::make('title')->required(),
                                                TextInput::make('tagline'),
                                                Textarea::make('content')->required(),
                                                \Filament\Forms\Components\FileUpload::make('image')
                                                    ->image()
                                                    ->directory('pages')
                                                    ->saveUploadedFileUsing(fn ($file) => \App\Services\ImageService::convertToWebp($file, 'pages')),
                                                \Filament\Forms\Components\Select::make('layout')
                                                    ->options([
                                                        'text_left' => 'Text Left, Image Right',
                                                        'text_right' => 'Text Right, Image Left',
                                                    ])->default('text_left'),
                                            ]),
                                        \Filament\Forms\Components\Builder\Block::make('stats')
                                            ->label('Stats Section')
                                            ->schema([
                                                TextInput::make('title'),
                                                \Filament\Forms\Components\Repeater::make('items')
                                                    ->schema([
                                                        TextInput::make('label')->required(),
                                                        TextInput::make('value')->required(),
                                                        TextInput::make('icon')->label('Icon Name (e.g., heroicon-o-clock)'),
                                                    ])->columns(2),
                                            ]),
                                        \Filament\Forms\Components\Builder\Block::make('features')
                                            ->label('Features Section')
                                            ->schema([
                                                TextInput::make('title'),
                                                \Filament\Forms\Components\Repeater::make('items')
                                                    ->schema([
                                                        TextInput::make('title')->required(),
                                                        Textarea::make('description')->required(),
                                                        TextInput::make('icon')->label('Icon Name (Arabic)'),
                                                    ])->columns(1),
                                            ]),
                                        \Filament\Forms\Components\Builder\Block::make('journey')
                                            ->label('Journey / Timeline Section')
                                            ->schema([
                                                TextInput::make('title'),
                                                Textarea::make('description'),
                                                \Filament\Forms\Components\Repeater::make('items')
                                                    ->schema([
                                                        TextInput::make('year')->required(),
                                                        \Filament\Forms\Components\FileUpload::make('planet')->label('Planet Image')->image()
                                                            ->directory('pages')
                                                            ->saveUploadedFileUsing(fn ($file) => \App\Services\ImageService::convertToWebp($file, 'pages')),
                                                        TextInput::make('location')->required(),
                                                        TextInput::make('users')->required(),
                                                        TextInput::make('partnership'),
                                                        Textarea::make('description')->required(),
                                                    ])->columns(2),
                                            ]),
                                        \Filament\Forms\Components\Builder\Block::make('team')
                                            ->label('Team / Founders Section')
                                            ->schema([
                                                TextInput::make('title'),
                                                Textarea::make('description'),
                                                \Filament\Forms\Components\Repeater::make('items')
                                                    ->schema([
                                                        TextInput::make('name')->required(),
                                                        TextInput::make('role')->required(),
                                                        \Filament\Forms\Components\FileUpload::make('image')->image()->required()
                                                            ->directory('pages')
                                                            ->saveUploadedFileUsing(fn ($file) => \App\Services\ImageService::convertToWebp($file, 'pages')),
                                                    ])->columns(2),
                                            ]),
                                        \Filament\Forms\Components\Builder\Block::make('cta')
                                            ->label('Call To Action Section')
                                            ->schema([
                                                TextInput::make('title')->required(),
                                                Textarea::make('description'),
                                                TextInput::make('button_text'),
                                                TextInput::make('button_url'),
                                            ]),
                                    ]),
                            ]),
                    ])->columnSpanFull(),
            ]);
    }
}
