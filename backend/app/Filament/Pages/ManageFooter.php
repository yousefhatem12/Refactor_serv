<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

/**
 * @property \Filament\Schemas\Schema|\Filament\Forms\Form $form
 */
class ManageFooter extends Page implements HasForms
{
    use InteractsWithForms;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('super_admin') ?? false;
    }

    public static function getNavigationGroup(): ?string { return __('Footer'); }
    protected static ?int $navigationSort = 1;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-bars-3-bottom-right';
    public static function getNavigationLabel(): string { return __('Footer'); }
    protected string $view = 'filament.pages.manage-footer';

    public ?array $data = [];

    public function mount(): void
    {
        $keys = [
            'footer_heading_en',
            'footer_heading_ar',
            'footer_description_en',
            'footer_description_ar',
            'footer_button_text_en',
            'footer_button_text_ar',
            'footer_copyright_en',
            'footer_copyright_ar',
            'custom_footer_code',
        ];

        $settings = Setting::whereIn('key', $keys)->pluck('value', 'key')->toArray();

        $this->form->fill($settings);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Footer Content - English')
                    ->description('Manage English footer text and call-to-action')
                    ->schema([
                        TextInput::make('footer_heading_en')->label('Heading (EN)')->required(),
                        Textarea::make('footer_description_en')->label('Description (EN)')->required()->rows(3),
                        TextInput::make('footer_button_text_en')->label('Button Text (EN)')->required(),
                        TextInput::make('footer_copyright_en')->label('Copyright (EN)')->required(),
                    ])->columns(2),

                Section::make('Footer Content - Arabic (التعريب)')
                    ->description('Manage Arabic footer text and call-to-action')
                    ->schema([
                        TextInput::make('footer_heading_ar')->label('Heading (AR)')->required(),
                        Textarea::make('footer_description_ar')->label('Description (AR)')->required()->rows(3),
                        TextInput::make('footer_button_text_ar')->label('Button Text (AR)')->required(),
                        TextInput::make('footer_copyright_ar')->label('Copyright (AR)')->required(),
                    ])->columns(2),

                Section::make('Custom Code')
                    ->description('Inject custom HTML/JS code before the closing </body> tag')
                    ->schema([
                        \Filament\Forms\Components\Textarea::make('custom_footer_code')
                             ->label('Footer Code (e.g. Chat widgets, external scripts)')
                             ->rows(5)
                             ->columnSpanFull(),
                    ]),
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

            foreach ($data as $key => $value) {
                Setting::updateOrCreate(['key' => $key], ['value' => $value]);
            }

            Notification::make()
                ->title('Footer settings saved successfully')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error saving footer settings')
                ->danger()
                ->body($e->getMessage())
                ->send();
        }
    }
}
