<?php

namespace App\Filament\Pages;

use App\Models\Page as PageModel;
use App\Support\AboutPagePeople;
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
class ManageAboutPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static bool $shouldRegisterNavigation = false;
    public static function getNavigationGroup(): ?string { return __('Content'); }
    protected static ?int $navigationSort = 1;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-information-circle';
    public static function getNavigationLabel(): string { return __('About Us'); }
    protected string $view = 'filament.pages.manage-about-page';

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return new \Illuminate\Support\HtmlString('
            <span class="inline-flex items-center gap-3">
                <a href="' . SitePages::getUrl() . '" class="inline-flex items-center justify-center rounded-md bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-400 transition" style="width: 24px; height: 24px; min-width: 24px; min-height: 24px; display: inline-flex; align-items: center; justify-content: center;">
                    <svg class="rtl:rotate-180" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width: 14px; height: 14px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                    </svg>
                </a>
                <span>' . __('About Us') . '</span>
            </span>
        ');
    }

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

        $data = $this->record->toArray();
        $data['people'] = AboutPagePeople::extract($data['sections'] ?? []);

        $this->form->fill($data);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Tabs::make('Translations')
                    ->tabs([
                        \Filament\Schemas\Components\Tabs\Tab::make('Founders & Team')
                            ->schema([
                                \Filament\Forms\Components\Repeater::make('people.founders')
                                    ->label('Founders')
                                    ->helperText('Add each founder once. Only the name and position are translated; the image is shared between Arabic and English.')
                                    ->schema([
                                        TextInput::make('name_en')->label('Name (English)')->required(),
                                        TextInput::make('name_ar')->label('Name (Arabic)')->required()->extraInputAttributes(['dir' => 'rtl']),
                                        TextInput::make('role_en')->label('Position / Department (English)')->required(),
                                        TextInput::make('role_ar')->label('Position / Department (Arabic)')->required()->extraInputAttributes(['dir' => 'rtl']),
                                        FileUpload::make('image')
                                            ->label('Shared Image')
                                            ->image()
                                            ->directory('pages')
                                            ->saveUploadedFileUsing(fn ($file) => \App\Services\ImageService::convertToWebp($file, 'pages')),
                                    ])
                                    ->columns(2)
                                    ->collapsible()
                                    ->defaultItems(0),
                                \Filament\Forms\Components\Repeater::make('people.team')
                                    ->label('Team Members')
                                    ->helperText('Add each member once. The image, department filter, and social accounts are shared between Arabic and English.')
                                    ->schema([
                                        TextInput::make('name_en')->label('Name (English)')->required(),
                                        TextInput::make('name_ar')->label('Name (Arabic)')->required()->extraInputAttributes(['dir' => 'rtl']),
                                        TextInput::make('role_en')->label('Position / Department (English)')->required(),
                                        TextInput::make('role_ar')->label('Position / Department (Arabic)')->required()->extraInputAttributes(['dir' => 'rtl']),
                                        \Filament\Forms\Components\Select::make('dept')
                                            ->label('Department Filter')
                                            ->options([
                                                'management' => 'Management',
                                                'development' => 'Development',
                                                'design' => 'Design',
                                                'marketing' => 'Marketing',
                                            ])->required(),
                                        FileUpload::make('image')
                                            ->label('Shared Image')
                                            ->image()
                                            ->directory('pages')
                                            ->saveUploadedFileUsing(fn ($file) => \App\Services\ImageService::convertToWebp($file, 'pages')),
                                        TextInput::make('linkedin')->label('LinkedIn URL'),
                                        TextInput::make('github')->label('GitHub URL'),
                                        TextInput::make('twitter')->label('Twitter / X URL'),
                                    ])
                                    ->columns(2)
                                    ->collapsible()
                                    ->defaultItems(0),
                            ]),
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
                                        \Filament\Forms\Components\Builder\Block::make('global_reach')
                                            ->label('Global Reach / Map Section')
                                            ->schema([
                                                TextInput::make('title')->required(),
                                                Textarea::make('description'),
                                                \Filament\Forms\Components\Repeater::make('items')
                                                    ->schema([
                                                        TextInput::make('city')->required(),
                                                        TextInput::make('name')->label('Country Name')->required(),
                                                        TextInput::make('details')->required(),
                                                        TextInput::make('x')
                                                            ->label('X Coordinate (%)')
                                                            ->numeric()
                                                            ->helperText('Horizontal position (0-100) on the map'),
                                                        TextInput::make('y')
                                                            ->label('Y Coordinate (%)')
                                                            ->numeric()
                                                            ->helperText('Vertical position (0-100) on the map'),
                                                    ])->columns(2),
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
                                            ]),
                                        \Filament\Forms\Components\Builder\Block::make('team_work')
                                            ->label('Team Work Section')
                                            ->schema([
                                                TextInput::make('title')->required(),
                                                Textarea::make('description'),
                                                \Filament\Forms\Components\Repeater::make('pillars')
                                                    ->schema([
                                                        TextInput::make('title')->required(),
                                                        Textarea::make('description')->required(),
                                                        TextInput::make('icon')->label('Icon Name (e.g., FaUsers, FaCode)')->required(),
                                                    ])->columns(3),
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
                                        \Filament\Forms\Components\Builder\Block::make('global_reach')
                                            ->label('Global Reach / Map Section')
                                            ->schema([
                                                TextInput::make('title')->required(),
                                                Textarea::make('description'),
                                                \Filament\Forms\Components\Repeater::make('items')
                                                    ->schema([
                                                        TextInput::make('city')->required(),
                                                        TextInput::make('name')->label('Country Name')->required(),
                                                        TextInput::make('details')->required(),
                                                        TextInput::make('x')
                                                            ->label('X Coordinate (%)')
                                                            ->numeric()
                                                            ->helperText('Horizontal position (0-100) on the map'),
                                                        TextInput::make('y')
                                                            ->label('Y Coordinate (%)')
                                                            ->numeric()
                                                            ->helperText('Vertical position (0-100) on the map'),
                                                    ])->columns(2),
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
                                            ]),
                                        \Filament\Forms\Components\Builder\Block::make('team_work')
                                            ->label('Team Work Section')
                                            ->schema([
                                                TextInput::make('title')->required(),
                                                Textarea::make('description'),
                                                \Filament\Forms\Components\Repeater::make('pillars')
                                                    ->schema([
                                                        TextInput::make('title')->required(),
                                                        Textarea::make('description')->required(),
                                                        TextInput::make('icon')->label('Icon Name (e.g., FaUsers, FaCode)')->required(),
                                                    ])->columns(3),
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
            $data['sections'] = AboutPagePeople::sync(
                $data['sections'] ?? [],
                $data['people'] ?? [],
            );
            unset($data['people']);

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
