<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Setting;

$translations = [
    'hero_cta_text' => [
        'en' => 'Launch Your Project',
        'ar' => 'ابدأ مشروعك'
    ],
    'hero_video_button_text' => [
        'en' => 'Know more',
        'ar' => 'اعرف المزيد'
    ],
    'our_products_title' => [
        'en' => 'Our Products',
        'ar' => 'منتجاتنا'
    ],
    'our_products_description' => [
        'en' => 'Explore our powerful ready-made SaaS subscriptions and solutions tailored for your business needs. Get your digital presence running instantly with our proven platforms.',
        'ar' => 'استكشف اشتراكات وحلول SaaS القوية والجاهزة والمصممة خصيصًا لتلبية احتياجات عملك. ابدأ حضورك الرقمي على الفور من خلال منصاتنا المجربة.'
    ],
    'trans_home' => [
        'en' => 'Home',
        'ar' => 'الرئيسية'
    ],
    'trans_aboutUs' => [
        'en' => 'About us',
        'ar' => 'من نحن'
    ],
    'trans_services' => [
        'en' => 'Services',
        'ar' => 'الخدمات'
    ],
    'trans_portfolio' => [
        'en' => 'Portfolio',
        'ar' => 'أعمالنا'
    ],
    'trans_blog' => [
        'en' => 'Blog',
        'ar' => 'المدونة'
    ],
    'trans_faq' => [
        'en' => 'FAQ',
        'ar' => 'الأسئلة الشائعة'
    ],
    'trans_contactUs' => [
        'en' => 'Contact us',
        'ar' => 'اتصل بنا'
    ],
    'trans_ourProducts' => [
        'en' => 'Our Products',
        'ar' => 'منتجاتنا'
    ],
    'trans_bankAccounts' => [
        'en' => 'Bank Accounts',
        'ar' => 'الحسابات البنكية'
    ],
];

foreach ($translations as $key => $value) {
    Setting::updateOrCreate(
        ['key' => $key],
        [
            'value' => json_encode($value),
            'type' => 'text',
            'value_source' => 'db'
        ]
    );
}

echo "Translations seeded successfully!\n";
