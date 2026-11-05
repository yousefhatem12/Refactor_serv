<?php

namespace Database\Seeders;

use App\Models\BankAccount;
use App\Models\FooterLink;
use App\Models\Setting;
use App\Models\SocialLink;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class FooterSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Delete all existing footer links to start fresh
        Schema::disableForeignKeyConstraints();
        FooterLink::truncate();
        BankAccount::truncate();
        Schema::enableForeignKeyConstraints();

        // 2. Define the exact footer structure from the design
        $footerData = [
            'useful_links' => [
                ['en' => 'Landing Page', 'ar' => 'صفحة الهبوط', 'url' => '/services/web-design'],
                ['en' => 'Popup Builder', 'ar' => 'منشئ النوافذ المنبثقة', 'url' => '/services/web-development'],
                ['en' => 'Web-design', 'ar' => 'تصميم الويب', 'url' => '/services/web-design'],
                ['en' => 'Content', 'ar' => 'إدارة المحتوى الرقمي', 'url' => '/services/digital-marketing'],
                ['en' => 'Integrations', 'ar' => 'الربط والتكامل', 'url' => '/services/web-development'],
            ],
            'quick_links' => [
                ['en' => 'Web-designers', 'ar' => 'تصميم المواقع', 'url' => '/services/web-design'],
                ['en' => 'Marketers', 'ar' => 'التسويق الإلكتروني', 'url' => '/services/digital-marketing'],
                ['en' => 'Online Stores', 'ar' => 'المتاجر الإلكترونية', 'url' => '/services/ecommerce'],
                ['en' => 'Mobile Apps', 'ar' => 'تطبيقات الجوال', 'url' => '/services/mobile-apps'],
            ],
            'offers' => [
                ['en' => 'Our Products', 'ar' => 'منتجاتنا', 'url' => '/our-products'],
                ['en' => 'Blog', 'ar' => 'المدونة', 'url' => '/blog'],
                ['en' => 'Our Works', 'ar' => 'سابقة أعمالنا', 'url' => '/portfolio'],
                ['en' => 'Hosting', 'ar' => 'استضافة المواقع', 'url' => '/services/web-hosting'],
                ['en' => 'SEO Optimization', 'ar' => 'تحسين محركات البحث SEO', 'url' => '/services/seo-optimization'],
                ['en' => 'Support', 'ar' => 'الدعم الفني', 'url' => '/services/aldaam-alfny'],
            ],
            'company' => [
                ['en' => 'About Us', 'ar' => 'عن الشركة', 'url' => '/about-us'],
                ['en' => 'Careers', 'ar' => 'الوظائف', 'url' => '/careers'],
                ['en' => 'FAQ', 'ar' => 'الأسئلة الشائعة', 'url' => '/faq'],
                ['en' => 'Bank Accounts', 'ar' => 'الحسابات البنكية', 'url' => '/bank-accounts'],
                ['en' => 'Contact Us', 'ar' => 'اتصل بنا', 'url' => '/contact-us'],
            ],
        ];

        // 3. Insert links into the database
        foreach ($footerData as $columnName => $links) {
            foreach ($links as $index => $item) {
                FooterLink::create([
                    'column_name' => $columnName,
                    'label' => ['en' => $item['en'], 'ar' => $item['ar']],
                    'url' => $item['url'],
                    'sort_order' => $index,
                ]);
            }
        }

        // 4. Update Contact Info Settings (from serv5.com XML)
        Setting::updateOrCreate(['key' => 'site_phone'], ['value' => '00201288863631', 'type' => 'text', 'value_source' => 'link']);
        Setting::updateOrCreate(['key' => 'site_whatsapp'], ['value' => '201288863631', 'type' => 'text', 'value_source' => 'link']);
        Setting::updateOrCreate(['key' => 'site_email'], ['value' => 'sales@serv5.com', 'type' => 'text', 'value_source' => 'link']);
        Setting::updateOrCreate(
            ['key' => 'site_address'],
            [
                'value' => json_encode([
                    'en' => 'Egypt - Mansoura - Suez Canal St., above Dr. Rami Pharmacy',
                    'ar' => 'مصر - المنصورة - شارع قناة السويس - أعلى صيدلية د. رامي',
                ], JSON_UNESCAPED_UNICODE),
                'type' => 'text',
                'value_source' => 'link',
            ]
        );

        // 5. Update Footer Translation Settings
        Setting::updateOrCreate(['key' => 'footer_heading_en'], ['value' => "Have a Good Web \n Design Today", 'type' => 'text']);
        Setting::updateOrCreate(['key' => 'footer_heading_ar'], ['value' => "احصل على تصميم موقع \n احترافي اليوم مع سيرف فايف", 'type' => 'text']);

        Setting::updateOrCreate(['key' => 'footer_description_en'], ['value' => 'Serv5 Company for Web Design, Web Programming, Mobile Applications, and Digital Marketing Services.', 'type' => 'text']);
        Setting::updateOrCreate(['key' => 'footer_description_ar'], ['value' => 'شركة سيرف فايف لتصميم وبرمجة المواقع وتطبيقات الجوال وخدمات التسويق الإلكتروني والاستضافة.', 'type' => 'text']);

        Setting::updateOrCreate(['key' => 'footer_button_text_en'], ['value' => 'Get started', 'type' => 'text']);
        Setting::updateOrCreate(['key' => 'footer_button_text_ar'], ['value' => 'ابدأ مشروعك الآن', 'type' => 'text']);

        Setting::updateOrCreate(['key' => 'footer_copyright_en'], ['value' => '© 2026 Serv5. All Rights Reserved', 'type' => 'text']);
        Setting::updateOrCreate(['key' => 'footer_copyright_ar'], ['value' => '© 2026 سيرف فايف. جميع الحقوق محفوظة', 'type' => 'text']);

        // 6. Seed Social Links
        $socials = [
            ['platform' => 'facebook', 'url' => 'https://www.facebook.com/serv5com'],
            ['platform' => 'twitter', 'url' => 'https://twitter.com/serv5com'],
            ['platform' => 'instagram', 'url' => 'https://www.instagram.com/serv5com'],
            ['platform' => 'linkedin', 'url' => 'https://www.linkedin.com/company/serv5'],
            ['platform' => 'youtube', 'url' => 'https://www.youtube.com/@serv5'],
        ];
        foreach ($socials as $social) {
            SocialLink::updateOrCreate(
                ['platform' => $social['platform']],
                ['url' => $social['url'], 'is_active' => true]
            );
        }

        // 7. Seed Bank Accounts extracted from serv5.com XML (Page 339 AR & Page 10812 EN)
        $bankFile = database_path('seeders/data/bank_accounts.json');
        if (file_exists($bankFile)) {
            $banks = json_decode((string) file_get_contents($bankFile), true);
            if (is_array($banks)) {
                foreach ($banks as $bank) {
                    BankAccount::create($bank);
                }
            }
        }
    }
}
