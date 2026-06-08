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

/**
 * @property \Filament\Schemas\Schema|\Filament\Forms\Form $form
 */
class ManageHomeHero extends Page implements HasForms
{
    use InteractsWithForms;

    protected static bool $shouldRegisterNavigation = false;
    public static function getNavigationGroup(): ?string { return __('Home Page'); }
    protected static ?int $navigationSort = 1;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-sparkles';
    public static function getNavigationLabel(): string { return __('Hero Section'); }
    protected string $view = 'filament.pages.manage-home-page';

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return new \Illuminate\Support\HtmlString('
            <span class="inline-flex items-center gap-3">
                <a href="' . ManageHomePage::getUrl() . '" class="inline-flex items-center justify-center rounded-md bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-400 transition" style="width: 24px; height: 24px; min-width: 24px; min-height: 24px; display: inline-flex; align-items: center; justify-content: center;">
                    <svg class="rtl:rotate-180" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width: 14px; height: 14px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                    </svg>
                </a>
                <span>' . __('Hero Section') . '</span>
            </span>
        ');
    }

    public ?array $data = [];
    public ?PageModel $record = null;

    public function mount(): void
    {
        $this->record = PageModel::firstOrCreate(
            ['slug' => 'home'],
            [
                'title' => ['en' => 'Home', 'ar' => 'الرئيسية'],
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
                                TextInput::make('title.en')->label('Hero Title Line 1'),
                                TextInput::make('subtitle.en')->label('Hero Title Line 2'),
                                Textarea::make('content.en')->label('Hero Description')->rows(3),
                                TextInput::make('seo_title.en')->label('SEO Title'),
                                Textarea::make('seo_description.en')->label('SEO Description'),
                                \Filament\Forms\Components\Builder::make('sections.en')
                                    ->label('Page Sections')
                                    ->blocks([
                                        \Filament\Forms\Components\Builder\Block::make('text_image')
                                            ->label('Text & Image Section')
                                            ->schema([
                                                TextInput::make('tagline'),
                                                TextInput::make('title')->required(),
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
                                TextInput::make('title.ar')->label('العنوان الرئيسي الأول'),
                                TextInput::make('subtitle.ar')->label('العنوان الرئيسي الثاني'),
                                Textarea::make('content.ar')->label('الوصف الرئيسي')->rows(3),
                                TextInput::make('seo_title.ar')->label('عنوان SEO'),
                                Textarea::make('seo_description.ar')->label('وصف SEO'),
                                \Filament\Forms\Components\Builder::make('sections.ar')
                                    ->label('Page Sections (Arabic)')
                                    ->blocks([
                                        \Filament\Forms\Components\Builder\Block::make('text_image')
                                            ->label('Text & Image Section')
                                            ->schema([
                                                TextInput::make('tagline'),
                                                TextInput::make('title')->required(),
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
                ->title('Hero section saved successfully')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error saving hero section')
                ->danger()
                ->body($e->getMessage())
                ->send();
        }
    }
}
