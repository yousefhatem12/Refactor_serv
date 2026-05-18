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

class ManageFooter extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\UnitEnum|null $navigationGroup = null;
    protected static ?int $navigationSort = 6;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-bars-3-bottom-right';
    public static function getNavigationLabel(): string { return __('Footer'); }
    protected string $view = 'filament.pages.manage-footer';

    public ?array $data = [];

    public function mount(): void
    {
        $keys = [
            'footer_heading',
            'footer_description',
            'footer_button_text',
            'footer_copyright',
            'site_phone',
            'site_email',
            'site_address',
            'custom_footer_code',
        ];

        $settings = Setting::whereIn('key', $keys)->get()->pluck('value', 'key')->toArray();

        $this->form->fill($settings);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Footer Content')
                    ->description('Manage footer text and call-to-action')
                    ->schema([
                        TextInput::make('footer_heading')->required(),
                        Textarea::make('footer_description')->required()->rows(3),
                        TextInput::make('footer_button_text')->required(),
                        TextInput::make('footer_copyright')->required(),
                    ])->columns(2),

                Section::make('Contact Information')
                    ->description('Global contact details used across the site')
                    ->schema([
                        TextInput::make('site_phone')->required(),
                        TextInput::make('site_email')->email()->required(),
                        TextInput::make('site_address')->required(),
                    ])->columns(3),

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
