<?php

namespace App\Filament\Pages;

use App\Models\Page as PageModel;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class ManageAboutPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\UnitEnum|null $navigationGroup = null;
    protected static ?int $navigationSort = 3;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-information-circle';
    public static function getNavigationLabel(): string { return __('About Us'); }
    protected string $view = 'filament.pages.manage-about-page';

    public ?array $data = [];
    public ?PageModel $record = null;

    public function mount(): void
    {
        $this->record = PageModel::firstOrCreate(
            ['slug' => 'about-us'],
            [
                'title' => ['en' => 'About Us', 'ar' => 'من نحن'],
                'subtitle' => ['en' => '', 'ar' => ''],
                'content' => ['en' => '', 'ar' => ''],
                'sections' => ['en' => [], 'ar' => []],
            ]
        );

        $this->form->fill($this->record->toArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Tabs::make('Translations')
                    ->tabs([
                        \Filament\Schemas\Components\Tabs\Tab::make('English')
                            ->schema([
                                TextInput::make('title.en')->label('Hero Title'),
                                TextInput::make('subtitle.en')->label('Hero Subtitle'),
                                Textarea::make('content.en')->label('Hero Description')->rows(3),
                                FileUpload::make('hero_image')
                                    ->label('Hero Image')
                                    ->image()
                                    ->directory('pages')
                                    ->saveUploadedFileUsing(fn ($file) => \App\Services\ImageService::convertToWebp($file, 'pages')),
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
                                                        \Filament\Forms\Components\FileUpload::make('image')->image()
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
                                TextInput::make('title.ar')->label('العنوان الرئيسي'),
                                TextInput::make('subtitle.ar')->label('العنوان الفرعي'),
                                Textarea::make('content.ar')->label('الوصف الرئيسي')->rows(3),
                                TextInput::make('seo_title.ar')->label('عنوان SEO'),
                                Textarea::make('seo_description.ar')->label('وصف SEO'),
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
                                                        \Filament\Forms\Components\FileUpload::make('image')->image()
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
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label(__('filament-panels::resources/pages/edit-record.form.actions.save.label'))
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        try {
            $data = $this->form->getState();
            $this->record->update($data);

            Notification::make()
                ->title('About Us page content saved successfully')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error saving About Us page content')
                ->danger()
                ->body($e->getMessage())
                ->send();
        }
    }
}
