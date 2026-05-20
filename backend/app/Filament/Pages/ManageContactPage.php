<?php

namespace App\Filament\Pages;

use App\Models\Page as PageModel;
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

class ManageContactPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static bool $shouldRegisterNavigation = false;
    public static function getNavigationGroup(): ?string { return __('Pages'); }
    protected static ?int $navigationSort = 6;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-phone-arrow-up-right';
    public static function getNavigationLabel(): string { return __('Contact Us'); }
    protected string $view = 'filament.pages.manage-contact-page';

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return new \Illuminate\Support\HtmlString('
            <span class="inline-flex items-center gap-3">
                <a href="' . SitePages::getUrl() . '" class="inline-flex items-center justify-center rounded-md bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-400 transition" style="width: 24px; height: 24px; min-width: 24px; min-height: 24px; display: inline-flex; align-items: center; justify-content: center;">
                    <svg class="rtl:rotate-180" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width: 14px; height: 14px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                    </svg>
                </a>
                <span>' . __('Contact Us') . '</span>
            </span>
        ');
    }

    public ?array $data = [];
    public ?PageModel $record = null;

    public function mount(): void
    {
        $this->record = PageModel::firstOrCreate(
            ['slug' => 'contact-us'],
            [
                'title' => ['en' => 'Let\'s Get in Touch', 'ar' => 'تواصل معنا'],
                'subtitle' => ['en' => 'Enim tempor eget pharetra facilisis sed maecenas adipiscing.', 'ar' => 'نحن هنا للإجابة على استفساراتكم.'],
                'content' => ['en' => '', 'ar' => ''],
                'sections' => ['en' => [], 'ar' => []],
            ]
        );

        $keys = [
            'site_phone',
            'site_whatsapp',
            'site_email',
            'site_skype',
            'site_address',
        ];

        $settings = Setting::whereIn('key', $keys)->get()->pluck('value', 'key')->toArray();
        
        $mergedData = array_merge($this->record->toArray(), $settings);

        $this->form->fill($mergedData);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Tabs::make('Settings')
                    ->tabs([
                        \Filament\Schemas\Components\Tabs\Tab::make('Page Content')
                            ->label('محتوى الصفحة / Content')
                            ->schema([
                                \Filament\Schemas\Components\Tabs::make('Translations')
                                    ->tabs([
                                        \Filament\Schemas\Components\Tabs\Tab::make('English')
                                            ->schema([
                                                TextInput::make('title.en')->label('Title')->required(),
                                                Textarea::make('subtitle.en')->label('Subtitle / Description')->rows(2),
                                                TextInput::make('seo_title.en')->label('SEO Title'),
                                                Textarea::make('seo_description.en')->label('SEO Description')->rows(2),
                                            ]),
                                        \Filament\Schemas\Components\Tabs\Tab::make('Arabic')
                                            ->schema([
                                                TextInput::make('title.ar')->label('العنوان')->required(),
                                                Textarea::make('subtitle.ar')->label('الوصف / النص الفرعي')->rows(2),
                                                TextInput::make('seo_title.ar')->label('عنوان SEO'),
                                                Textarea::make('seo_description.ar')->label('وصف SEO')->rows(2),
                                            ]),
                                    ])->columnSpanFull(),
                            ]),
                        \Filament\Schemas\Components\Tabs\Tab::make('Contact Details')
                            ->label('بيانات الاتصال / Contact Details')
                            ->schema([
                                TextInput::make('site_phone')->label('Phone Number / رقم الهاتف'),
                                TextInput::make('site_whatsapp')->label('WhatsApp Number / رقم الواتساب'),
                                TextInput::make('site_email')->label('Email Address / البريد الإلكتروني')->email(),
                                TextInput::make('site_skype')->label('Skype ID / حساب سكايب'),
                                Textarea::make('site_address')->label('Address / العنوان')->rows(2)->columnSpanFull(),
                            ])->columns(2),
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
            
            // Save Page Model Data
            $pageData = collect($data)->only(['title', 'subtitle', 'seo_title', 'seo_description'])->toArray();
            $this->record->update($pageData);

            // Save Settings Data
            $settingsData = collect($data)->only(['site_phone', 'site_whatsapp', 'site_email', 'site_skype', 'site_address'])->toArray();
            foreach ($settingsData as $key => $value) {
                Setting::updateOrCreate(['key' => $key], ['value' => $value]);
            }

            Notification::make()
                ->title('Contact Us settings saved successfully')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error saving contact settings')
                ->danger()
                ->body($e->getMessage())
                ->send();
        }
    }
}
