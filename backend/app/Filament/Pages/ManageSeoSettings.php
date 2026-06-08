<?php

namespace App\Filament\Pages;

use App\Models\Page as PageModel;
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
class ManageSeoSettings extends Page implements HasForms
{
    use InteractsWithForms;

    public static function getNavigationGroup(): ?string { return __('System Settings'); }
    protected static ?int $navigationSort = 60;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-magnifying-glass';
    public static function getNavigationLabel(): string { return __('SEO Settings'); }
    protected string $view = 'filament.pages.manage-seo-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $pages = PageModel::all();
        $formData = [];
        foreach ($pages as $page) {
            $formData[$page->slug] = [
                'seo_title' => $page->seo_title ?? ['en' => '', 'ar' => ''],
                'seo_description' => $page->seo_description ?? ['en' => '', 'ar' => ''],
            ];
        }
        $this->form->fill($formData);
    }

    public function form(Schema $schema): Schema
    {
        $tabs = [];
        $slugs = [
            'home' => 'Home Page / الرئيسية', 
            'about-us' => 'About Us / من نحن', 
            'portfolio' => 'Our Work / أعمالنا', 
            'blog' => 'Blog / المدونة', 
            'faq' => 'FAQs / الأسئلة الشائعة', 
            'contact-us' => 'Contact Us / اتصل بنا'
        ];

        foreach ($slugs as $slug => $label) {
            $tabs[] = \Filament\Schemas\Components\Tabs\Tab::make($label)
                ->schema([
                    \Filament\Schemas\Components\Section::make('English SEO')
                        ->schema([
                            TextInput::make("$slug.seo_title.en")
                                ->label('SEO Title (EN)')
                                ->placeholder('Enter search engine title in English'),
                            Textarea::make("$slug.seo_description.en")
                                ->label('Meta Description (EN)')
                                ->placeholder('Enter search engine description in English')
                                ->rows(3),
                        ]),
                    \Filament\Schemas\Components\Section::make('Arabic SEO')
                        ->schema([
                            TextInput::make("$slug.seo_title.ar")
                                ->label('SEO Title (AR)')
                                ->placeholder('اكتب عنوان محركات البحث بالعربية'),
                            Textarea::make("$slug.seo_description.ar")
                                ->label('Meta Description (AR)')
                                ->placeholder('اكتب وصف محركات البحث بالعربية')
                                ->rows(3),
                        ]),
                ]);
        }

        return $schema
            ->components([
                \Filament\Schemas\Components\Tabs::make('SEO Pages')
                    ->tabs($tabs)
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

            foreach ($data as $slug => $seoData) {
                $page = PageModel::where('slug', $slug)->first();
                if ($page) {
                    $page->update([
                        'seo_title' => $seoData['seo_title'],
                        'seo_description' => $seoData['seo_description'],
                    ]);
                }
            }

            Notification::make()
                ->title('SEO settings saved successfully')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error saving SEO settings')
                ->danger()
                ->body($e->getMessage())
                ->send();
        }
    }
}
