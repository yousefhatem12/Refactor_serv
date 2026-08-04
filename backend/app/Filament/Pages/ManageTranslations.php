<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

/**
 * @property Schema $form
 */
class ManageTranslations extends Page implements HasForms
{
    use InteractsWithForms;

    public static function getNavigationGroup(): ?string { return __('System Settings'); }
    protected static ?int $navigationSort = 80;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-language';
    public static function getNavigationLabel(): string { return __('Translations / التراجم'); }
    protected string $view = 'filament.pages.manage-translations';

    public ?array $data = [];

    public function mount(): void
    {
        $keys = [
            'hero_cta_text',
            'hero_video_button_text',
            'our_products_title',
            'our_products_description',
            
            // Translations.js Overrides
            'trans_home',
            'trans_aboutUs',
            'trans_services',
            'trans_portfolio',
            'trans_blog',
            'trans_faq',
            'trans_contactUs',
            'trans_ourProducts',
            'trans_bankAccounts',
            'trans_wdPortfolioTitle',
            'trans_maPortfolioTitle',
            'trans_wdPortfolioDesc',
            'trans_maPortfolioDesc',
            'trans_home_blog_first',
            'trans_home_blog_second',
        ];

        $settingsData = Setting::whereIn('key', $keys)->pluck('value', 'key')->toArray();

        $formData = [];
        foreach ($keys as $key) {
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
            ->schema([
                Section::make('Hero Buttons / أزرار الهيرو')
                    ->description('Translate the hero CTA and video buttons')
                    ->schema([
                        TextInput::make('hero_cta_text.en')->label('CTA Button (English)'),
                        TextInput::make('hero_cta_text.ar')->label('CTA Button (Arabic)'),
                        TextInput::make('hero_video_button_text.en')->label('Video Button (English)'),
                        TextInput::make('hero_video_button_text.ar')->label('Video Button (Arabic)'),
                    ])->columns(2),

                Section::make('Our Products Page Header / رأس صفحة منتجاتنا')
                    ->description('Translate the title and description of the Products page')
                    ->schema([
                        TextInput::make('our_products_title.en')->label('Title (English)'),
                        TextInput::make('our_products_title.ar')->label('Title (Arabic)'),
                        TextInput::make('our_products_description.en')->label('Description (English)'),
                        TextInput::make('our_products_description.ar')->label('Description (Arabic)'),
                    ])->columns(2),

                Section::make('Website Main Menu / القائمة الرئيسية للموقع')
                    ->description('Translate the navbar links')
                    ->schema([
                        TextInput::make('trans_home.en')->label('Home (EN)'),
                        TextInput::make('trans_home.ar')->label('الرئيسية (AR)'),
                        
                        TextInput::make('trans_aboutUs.en')->label('About Us (EN)'),
                        TextInput::make('trans_aboutUs.ar')->label('من نحن (AR)'),
                        
                        TextInput::make('trans_services.en')->label('Services (EN)'),
                        TextInput::make('trans_services.ar')->label('الخدمات (AR)'),
                        
                        TextInput::make('trans_portfolio.en')->label('Portfolio (EN)'),
                        TextInput::make('trans_portfolio.ar')->label('أعمالنا (AR)'),
                        
                        TextInput::make('trans_ourProducts.en')->label('Our Products (EN)'),
                        TextInput::make('trans_ourProducts.ar')->label('منتجاتنا (AR)'),
                        
                        TextInput::make('trans_blog.en')->label('Blog (EN)'),
                        TextInput::make('trans_blog.ar')->label('المدونة (AR)'),
                        
                        TextInput::make('trans_faq.en')->label('FAQ (EN)'),
                        TextInput::make('trans_faq.ar')->label('الأسئلة الشائعة (AR)'),
                        
                        TextInput::make('trans_contactUs.en')->label('Contact Us (EN)'),
                        TextInput::make('trans_contactUs.ar')->label('اتصل بنا (AR)'),

                        TextInput::make('trans_bankAccounts.en')->label('Bank Accounts (EN)'),
                        TextInput::make('trans_bankAccounts.ar')->label('الحسابات البنكية (AR)'),
                    ])->columns(2),

                Section::make('Portfolio Section Titles / عناوين أقسام الأعمال')
                    ->description('Translate the portfolio section titles for Web and Mobile')
                    ->schema([
                        TextInput::make('trans_wdPortfolioTitle.en')->label('Web Design Portfolio Title (EN)'),
                        TextInput::make('trans_wdPortfolioTitle.ar')->label('عنوان أعمال تصميم المواقع (AR)'),
                        
                        TextInput::make('trans_wdPortfolioDesc.en')->label('Web Design Portfolio Description (EN)'),
                        TextInput::make('trans_wdPortfolioDesc.ar')->label('وصف أعمال تصميم المواقع (AR)'),
                        
                        TextInput::make('trans_maPortfolioTitle.en')->label('Mobile App Portfolio Title (EN)'),
                        TextInput::make('trans_maPortfolioTitle.ar')->label('عنوان أعمال تطبيقات الجوال (AR)'),
                        
                        TextInput::make('trans_maPortfolioDesc.en')->label('Mobile App Portfolio Description (EN)'),
                        TextInput::make('trans_maPortfolioDesc.ar')->label('وصف أعمال تطبيقات الجوال (AR)'),
                    ])->columns(2),

                Section::make('Home Blog Section Title / عنوان قسم المدونة بالرئيسية')
                    ->description('Translate the title of the Blog section on the Homepage')
                    ->schema([
                        TextInput::make('trans_home_blog_first.en')->label('First Word (EN) e.g. "Our"'),
                        TextInput::make('trans_home_blog_first.ar')->label('الكلمة الأولى (AR) مثل "أحدث"'),
                        
                        TextInput::make('trans_home_blog_second.en')->label('Second Word (EN) e.g. "Blog"'),
                        TextInput::make('trans_home_blog_second.ar')->label('الكلمة الثانية (AR) مثل "المقالات"'),
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
                Setting::updateOrCreate(
                    ['key' => $key],
                    [
                        'value' => json_encode($value),
                        'type' => 'text',
                        'value_source' => 'db'
                    ]
                );
            }

            Notification::make()
                ->title('Translations saved successfully')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error saving translations')
                ->danger()
                ->body($e->getMessage())
                ->send();
        }
    }
}
