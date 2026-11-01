<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FooterLink;
use App\Models\Setting;

class FooterSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Delete all existing footer links to start fresh
        FooterLink::truncate();

        // 2. Define the exact footer structure from the design
        $footerData = [
            'useful_links' => [
                ['en' => 'Landing Page', 'ar' => 'صفحة الهبوط'],
                ['en' => 'Popup Builder', 'ar' => 'منشئ النوافذ المنبثقة'],
                ['en' => 'Web-design', 'ar' => 'تصميم الويب'],
                ['en' => 'Content', 'ar' => 'المحتوى'],
                ['en' => 'Integrations', 'ar' => 'الربط والتكامل'],
            ],
            'quick_links' => [
                ['en' => 'Web-designers', 'ar' => 'مصممي الويب'],
                ['en' => 'Marketers', 'ar' => 'المسوقين'],
                ['en' => 'Small Business', 'ar' => 'الأعمال الصغيرة'],
                ['en' => 'Website Builder', 'ar' => 'منشئ المواقع'],
            ],
            'offers' => [
                ['en' => 'Academy', 'ar' => 'الأكاديمية'],
                ['en' => 'Blog', 'ar' => 'المدونة'],
                ['en' => 'Themes', 'ar' => 'القوالب'],
                ['en' => 'Hosting', 'ar' => 'الاستضافة'],
                ['en' => 'Developers', 'ar' => 'المطورين'],
                ['en' => 'Support', 'ar' => 'الدعم الفني'],
            ],
            'company' => [
                ['en' => 'About Us', 'ar' => 'من نحن'],
                ['en' => 'Careers', 'ar' => 'الوظائف'],
                ['en' => 'FAQ', 'ar' => 'الأسئلة الشائعة'],
                ['en' => 'Teams', 'ar' => 'فريق العمل'],
                ['en' => 'Contact Us', 'ar' => 'اتصل بنا'],
            ],
        ];

        // 3. Insert links into the database
        foreach ($footerData as $columnName => $links) {
            foreach ($links as $index => $label) {
                $url = strtolower(str_replace(' ', '-', $label['en']));
                FooterLink::create([
                    'column_name' => $columnName,
                    'label' => $label, 
                    'url' => '/' . $url, 
                    'sort_order' => $index,
                ]);
            }
        }

        // 4. Update Contact Info Settings
        Setting::updateOrCreate(['key' => 'site_phone'], ['value' => '00201288863831', 'type' => 'text', 'value_source' => 'link']);
        Setting::updateOrCreate(['key' => 'site_email'], ['value' => 'sales@serv5.com', 'type' => 'text', 'value_source' => 'link']);
        Setting::updateOrCreate(['key' => 'site_address'], ['value' => json_encode(['en' => 'Egypt-Mansoura-Suez Canal st. above Dr.Ramy pharmacy', 'ar' => 'مصر - المنصورة - شارع قناة السويس - أعلى صيدلية د. رامي']), 'type' => 'text', 'value_source' => 'link']);

        // 5. Update Footer Translation Settings
        Setting::updateOrCreate(['key' => 'footer_heading_en'], ['value' => "Have a Good Web \n Design Today", 'type' => 'text']);
        Setting::updateOrCreate(['key' => 'footer_heading_ar'], ['value' => "احصل على تصميم ويب \n رائع اليوم", 'type' => 'text']);
        
        Setting::updateOrCreate(['key' => 'footer_description_en'], ['value' => 'High level experience in web design and development knowledge, producing quality work.', 'type' => 'text']);
        Setting::updateOrCreate(['key' => 'footer_description_ar'], ['value' => 'خبرة عالية المستوى في معرفة تصميم وتطوير الويب، وإنتاج أعمال عالية الجودة.', 'type' => 'text']);
        
        Setting::updateOrCreate(['key' => 'footer_button_text_en'], ['value' => 'Get started', 'type' => 'text']);
        Setting::updateOrCreate(['key' => 'footer_button_text_ar'], ['value' => 'ابدأ الآن', 'type' => 'text']);
        
        Setting::updateOrCreate(['key' => 'footer_copyright_en'], ['value' => '© 2024 All Rights Reserved', 'type' => 'text']);
        Setting::updateOrCreate(['key' => 'footer_copyright_ar'], ['value' => '© 2024 جميع الحقوق محفوظة', 'type' => 'text']);

        // 6. Seed Social Links
        $socials = [
            ['platform' => 'facebook', 'url' => 'https://facebook.com/serv5'],
            ['platform' => 'twitter', 'url' => 'https://twitter.com/serv5'],
            ['platform' => 'instagram', 'url' => 'https://instagram.com/serv5'],
            ['platform' => 'linkedin', 'url' => 'https://linkedin.com/company/serv5'],
            ['platform' => 'youtube', 'url' => 'https://youtube.com/@serv5'],
        ];
        foreach ($socials as $social) {
            \App\Models\SocialLink::updateOrCreate(
                ['platform' => $social['platform']],
                ['url' => $social['url'], 'is_active' => true]
            );
        }
    }
}
