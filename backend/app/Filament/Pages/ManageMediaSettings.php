<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class ManageMediaSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\UnitEnum|null $navigationGroup = null;
    protected static ?int $navigationSort = 16;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-photo';
    public static function getNavigationLabel(): string { return __('Images & Media'); }
    protected string $view = 'filament.pages.manage-media-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $keys = [
            'site_logo',
            'site_favicon',
            'social_share_image',
            'company_video_url',
        ];

        $settings = Setting::whereIn('key', $keys)->get()->pluck('value', 'key')->toArray();

        $this->form->fill($settings);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                \Filament\Schemas\Components\Section::make('Branding Media')
                    ->description('Manage global logos and icons')
                    ->schema([
                        FileUpload::make('site_logo')
                            ->label('Website Logo')
                            ->image()
                            ->directory('settings')
                            ->saveUploadedFileUsing(fn ($file) => \App\Services\ImageService::convertToWebp($file, 'settings')),
                        FileUpload::make('site_favicon')
                            ->label('Favicon (Tab Icon)')
                            ->image()
                            ->directory('settings')
                            ->saveUploadedFileUsing(fn ($file) => \App\Services\ImageService::convertToWebp($file, 'settings')),
                    ])->columns(2),

                \Filament\Schemas\Components\Section::make('SEO & Sharing Media')
                    ->description('Manage images used when sharing links on social media')
                    ->schema([
                        FileUpload::make('social_share_image')
                            ->label('Social Share Image (OG Image)')
                            ->image()
                            ->directory('settings')
                            ->saveUploadedFileUsing(fn ($file) => \App\Services\ImageService::convertToWebp($file, 'settings')),
                    ]),

                \Filament\Schemas\Components\Section::make('Interactive Media')
                    ->description('Global interactive media links')
                    ->schema([
                        TextInput::make('company_video_url')
                            ->label('Company Video URL (YouTube / Vimeo / Direct Link)')
                            ->url()
                            ->placeholder('https://www.youtube.com/watch?v=...'),
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
                ->title('Media settings saved successfully')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error saving media settings')
                ->danger()
                ->body($e->getMessage())
                ->send();
        }
    }
}
