<?php

namespace App\Filament\Pages;

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
class ManageHomeCTA extends Page implements HasForms
{
    use InteractsWithForms;

    protected static bool $shouldRegisterNavigation = false;
    public static function getNavigationGroup(): ?string { return __('Home Page'); }
    protected static ?int $navigationSort = 6;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-megaphone';
    public static function getNavigationLabel(): string { return __('Call To Action (CTA)'); }
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
                <span>' . __('Call To Action (CTA)') . '</span>
            </span>
        ');
    }

    public ?array $data = [];

    public function mount(): void
    {
        $settingsKeys = [
            'cta_ready_for',
            'cta_liftoff',
            'cta_desc_1',
            'cta_desc_2',
            'cta_unlock_power',
            'cta_innovation',
            'cta_get_started',
            'cta_long_desc',
        ];
        
        $settingsData = \App\Models\Setting::whereIn('key', $settingsKeys)->pluck('value', 'key')->toArray();

        $formData = [];
        foreach ($settingsKeys as $key) {
            $val = $settingsData[$key] ?? null;
            $formData[$key] = is_string($val) ? json_decode($val, true) : ['en' => '', 'ar' => ''];
            if (!is_array($formData[$key])) {
                $formData[$key] = ['en' => $val ?: '', 'ar' => $val ?: ''];
            }
        }

        $this->form->fill($formData);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('CTA Header (Ready For Liftoff?)')
                    ->schema([
                        TextInput::make('cta_ready_for.en')->label('Ready For (English)'),
                        TextInput::make('cta_ready_for.ar')->label('Ready For (Arabic)'),
                        TextInput::make('cta_liftoff.en')->label('Liftoff? (English)'),
                        TextInput::make('cta_liftoff.ar')->label('Liftoff? (Arabic)'),
                    ])->columns(2),

                \Filament\Schemas\Components\Section::make('CTA Descriptions')
                    ->schema([
                        Textarea::make('cta_desc_1.en')->label('Description Line 1 (English)')->rows(2),
                        Textarea::make('cta_desc_1.ar')->label('Description Line 1 (Arabic)')->rows(2),
                        Textarea::make('cta_desc_2.en')->label('Description Line 2 (English)')->rows(2),
                        Textarea::make('cta_desc_2.ar')->label('Description Line 2 (Arabic)')->rows(2),
                    ])->columns(2),

                \Filament\Schemas\Components\Section::make('CTA Bottom Subtitle & Long Description')
                    ->schema([
                        TextInput::make('cta_unlock_power.en')->label('Unlock Power Subtitle (English)'),
                        TextInput::make('cta_unlock_power.ar')->label('Unlock Power Subtitle (Arabic)'),
                        TextInput::make('cta_innovation.en')->label('Innovation Subtitle (English)'),
                        TextInput::make('cta_innovation.ar')->label('Innovation Subtitle (Arabic)'),
                        TextInput::make('cta_get_started.en')->label('Get Started Subtitle (English)'),
                        TextInput::make('cta_get_started.ar')->label('Get Started Subtitle (Arabic)'),
                        Textarea::make('cta_long_desc.en')->label('Long Description (English)')->rows(3)->columnSpanFull(),
                        Textarea::make('cta_long_desc.ar')->label('Long Description (Arabic)')->rows(3)->columnSpanFull(),
                    ])->columns(3),
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
            
            $settingsKeys = [
                'cta_ready_for',
                'cta_liftoff',
                'cta_desc_1',
                'cta_desc_2',
                'cta_unlock_power',
                'cta_innovation',
                'cta_get_started',
                'cta_long_desc',
            ];
            
            foreach ($settingsKeys as $key) {
                \App\Models\Setting::updateOrCreate(
                    ['key' => $key],
                    [
                        'value' => json_encode($data[$key] ?? ['en' => '', 'ar' => '']),
                        'type' => 'text',
                        'value_source' => 'db'
                    ]
                );
            }

            Notification::make()
                ->title('CTA settings saved successfully')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error saving CTA settings')
                ->danger()
                ->body($e->getMessage())
                ->send();
        }
    }
}
