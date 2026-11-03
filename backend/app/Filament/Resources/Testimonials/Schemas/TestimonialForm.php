<?php

namespace App\Filament\Resources\Testimonials\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class TestimonialForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Testimonial Details')
                    ->tabs([
                        Tab::make('English Content')
                            ->icon('heroicon-o-language')
                            ->schema([
                                TextInput::make('client_name.en')
                                    ->label(__('Client Name') . ' (EN)')
                                    ->required(),
                                TextInput::make('position.en')
                                    ->label(__('Position') . ' (EN)')
                                    ->default(null),
                                Textarea::make('review.en')
                                    ->label(__('Review') . ' (EN)')
                                    ->required()
                                    ->columnSpanFull(),
                            ]),
                        Tab::make('Arabic Content')
                            ->icon('heroicon-o-language')
                            ->schema([
                                TextInput::make('client_name.ar')
                                    ->label(__('Client Name') . ' (AR)')
                                    ->required(),
                                TextInput::make('position.ar')
                                    ->label(__('Position') . ' (AR)')
                                    ->default(null),
                                Textarea::make('review.ar')
                                    ->label(__('Review') . ' (AR)')
                                    ->required()
                                    ->columnSpanFull(),
                            ]),
                        Tab::make('Media & Settings')
                            ->icon('heroicon-o-film')
                            ->schema([
                                Select::make('rating')
                                    ->label(__('Rating (Stars)'))
                                    ->options([
                                        1 => '1 Star ⭐',
                                        2 => '2 Stars ⭐⭐',
                                        3 => '3 Stars ⭐⭐⭐',
                                        4 => '4 Stars ⭐⭐⭐⭐',
                                        5 => '5 Stars ⭐⭐⭐⭐⭐',
                                    ])
                                    ->default(5)
                                    ->required(),
                                TextInput::make('video_url')
                                    ->label(__('Video Link (YouTube/MP4)'))
                                    ->url()
                                    ->nullable(),
                                FileUpload::make('audio_url')
                                    ->label(__('Audio File (Voice Record)'))
                                    ->directory('testimonials/audio')
                                    ->nullable()
                                    ->acceptedFileTypes(['audio/mpeg', 'audio/mp3', 'audio/wav', 'audio/ogg']),
                                FileUpload::make('image')
                                    ->label(__('Client Image'))
                                    ->image()
                                    ->directory('testimonials')
                                    ->saveUploadedFileUsing(fn ($file) => \App\Services\ImageService::convertToWebp($file, 'testimonials'))
                                    ->nullable(),
                                Toggle::make('is_active')
                                    ->label(__('Active'))
                                    ->default(true)
                                    ->required(),
                            ])->columns(2),
                    ])->columnSpanFull()
            ]);
    }
}
