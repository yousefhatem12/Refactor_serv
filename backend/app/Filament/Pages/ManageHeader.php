<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use App\Services\ImageService;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

/**
 * @property Schema $form
 */
class ManageHeader extends Page implements HasForms
{
    use InteractsWithForms;

    public static function getNavigationGroup(): ?string
    {
        return __('Header');
    }

    protected static ?int $navigationSort = 1;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-bars-3';

    public static function getNavigationLabel(): string
    {
        return __('Header');
    }

    protected string $view = 'filament.pages.manage-header';

    public ?array $data = [];

    public function mount(): void
    {
        $keys = [
            'site_logo',
            'site_title',
            'custom_header_code',
        ];

        $settings = Setting::whereIn('key', $keys)->pluck('value', 'key')->toArray();

        $this->form->fill($settings);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Header Configuration')
                    ->description('Manage site identity and navigation header')
                    ->schema([
                        TextInput::make('site_title')
                            ->label('Website Title')
                            ->required(),
                        FileUpload::make('site_logo')
                            ->label('Website Logo')
                            ->image()
                            ->directory('settings')
                            ->saveUploadedFileUsing(fn ($file) => ImageService::convertToWebp($file, 'settings')),
                    ])->columns(2),
                Section::make('Custom Code')
                    ->description('Inject custom HTML/JS code into the <head> of the website')
                    ->schema([
                        Textarea::make('custom_header_code')
                            ->label('Header Code (e.g. Meta tags, Google Analytics)')
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
                $attributes = ['value' => $value];
                if ($key === 'site_logo') {
                    $attributes['type'] = 'image';
                    $attributes['value_source'] = 'file';
                }

                Setting::updateOrCreate(['key' => $key], $attributes);
            }

            Notification::make()
                ->title('Header settings saved successfully')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error saving header settings')
                ->danger()
                ->body($e->getMessage())
                ->send();
        }
    }
}
