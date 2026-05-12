<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class ManageHeader extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-bars-3';
    protected static string|\UnitEnum|null $navigationGroup = 'Header';
    protected static ?string $navigationLabel = 'Header Settings';
    protected static ?int $navigationSort = 1;
    protected string $view = 'filament.pages.manage-header';

    public ?array $data = [];

    public function mount(): void
    {
        $keys = [
            'site_logo',
            'site_title',
        ];

        $settings = Setting::whereIn('key', $keys)->get()->pluck('value', 'key')->toArray();

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

            foreach ($data as $key => $value) {
                Setting::updateOrCreate(['key' => $key], ['value' => $value]);
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
