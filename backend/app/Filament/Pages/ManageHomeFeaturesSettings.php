<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

/**
 * @property \Filament\Schemas\Schema|\Filament\Forms\Form $form
 */
class ManageHomeFeaturesSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static bool $shouldRegisterNavigation = false;
    public static function getNavigationGroup(): ?string { return __('Home Page'); }
    protected static ?int $navigationSort = 2;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-adjustments-horizontal';
    public static function getNavigationLabel(): string { return __('Features Settings'); }
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
                <span>' . __('Features Settings') . '</span>
            </span>
        ');
    }

    public ?array $data = [];

    public function mount(): void
    {
        $settingsKeys = [
            'features_title',
            'features_desc',
            'features_image',
            'features_partners',
        ];
        
        $settingsData = \App\Models\Setting::whereIn('key', $settingsKeys)->pluck('value', 'key')->toArray();

        $formData = [];
        foreach (['features_title', 'features_desc'] as $key) {
            $val = $settingsData[$key] ?? null;
            $formData[$key] = is_string($val) ? json_decode($val, true) : ['en' => '', 'ar' => ''];
            if (!is_array($formData[$key])) {
                $formData[$key] = ['en' => $val ?: '', 'ar' => $val ?: ''];
            }
        }

        $formData['features_image'] = $settingsData['features_image'] ?? null;
        
        $partnersVal = $settingsData['features_partners'] ?? null;
        $formData['features_partners'] = is_string($partnersVal) ? json_decode($partnersVal, true) : [];
        if (!is_array($formData['features_partners'])) {
            $formData['features_partners'] = [];
        }

        $this->form->fill($formData);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('Features Section Title & Description')
                    ->schema([
                        TextInput::make('features_title.en')->label('Title (English)'),
                        TextInput::make('features_title.ar')->label('Title (Arabic)'),
                        Textarea::make('features_desc.en')->label('Description (English)')->rows(3),
                        Textarea::make('features_desc.ar')->label('Description (Arabic)')->rows(3),
                    ])->columns(2),

                \Filament\Schemas\Components\Section::make('Features Images & Partners')
                    ->schema([
                        FileUpload::make('features_image')
                            ->label('Experience Image (e.g. 15 Years of Experience)')
                            ->image()
                            ->directory('settings')
                            ->saveUploadedFileUsing(fn ($file) => \App\Services\ImageService::convertToWebp($file, 'settings')),

                        FileUpload::make('features_partners')
                            ->label('Partners / Client Logos')
                            ->image()
                            ->multiple()
                            ->directory('settings')
                            ->saveUploadedFileUsing(fn ($file) => \App\Services\ImageService::convertToWebp($file, 'settings')),
                    ])->columns(2),
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
            
            foreach (['features_title', 'features_desc'] as $key) {
                \App\Models\Setting::updateOrCreate(
                    ['key' => $key],
                    [
                        'value' => json_encode($data[$key] ?? ['en' => '', 'ar' => '']),
                        'type' => 'text',
                        'value_source' => 'db'
                    ]
                );
            }

            \App\Models\Setting::updateOrCreate(
                ['key' => 'features_image'],
                [
                    'value' => $data['features_image'] ?? null,
                    'type' => 'image',
                    'value_source' => 'file'
                ]
            );

            \App\Models\Setting::updateOrCreate(
                ['key' => 'features_partners'],
                [
                    'value' => json_encode($data['features_partners'] ?? []),
                    'type' => 'image',
                    'value_source' => 'file'
                ]
            );

            Notification::make()
                ->title('Features settings saved successfully')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error saving features settings')
                ->danger()
                ->body($e->getMessage())
                ->send();
        }
    }
}
