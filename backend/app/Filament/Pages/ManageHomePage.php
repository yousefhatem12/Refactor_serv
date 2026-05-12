<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class ManageHomePage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-home';
    protected static string|\UnitEnum|null $navigationGroup = 'Home Page';
    protected static ?string $navigationLabel = 'Home Page Settings';
    protected static ?int $navigationSort = 1;
    protected string $view = 'filament.pages.manage-home-page';

    public ?array $data = [];

    public function mount(): void
    {
        $keys = [
            'hero_title_1',
            'hero_title_2',
            'hero_desc',
            'hero_planet',
            'hero_cta_text',
        ];

        $settings = Setting::whereIn('key', $keys)->get()->pluck('value', 'key')->toArray();

        $this->form->fill($settings);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Hero Section')
                    ->description('Manage the main banner of the home page')
                    ->schema([
                        TextInput::make('hero_title_1')
                            ->label('Hero Title Line 1')
                            ->required(),
                        TextInput::make('hero_title_2')
                            ->label('Hero Title Line 2')
                            ->required(),
                        Textarea::make('hero_desc')
                            ->label('Hero Description')
                            ->required()
                            ->rows(3),
                        TextInput::make('hero_cta_text')
                            ->label('CTA Button Text')
                            ->required(),
                        FileUpload::make('hero_planet')
                            ->label('Hero Planet Image')
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
                ->title('Settings saved successfully')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error saving settings')
                ->danger()
                ->body($e->getMessage())
                ->send();
        }
    }
}
