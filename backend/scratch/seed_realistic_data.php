<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Setting;
use App\Models\Service;
use App\Models\Project;
use App\Models\Feature;
use App\Models\Statistic;
use App\Models\SocialLink;
use App\Models\NavLink;
use App\Models\FooterLink;
use App\Models\Testimonial;
use App\Models\TeamMember;
use Illuminate\Support\Facades\DB;

// Disable foreign key checks
DB::statement('SET FOREIGN_KEY_CHECKS=0;');

// 1. Settings
$settings = [
    // Header (Removed to avoid overwriting branding)
    // ['key' => 'site_name', 'value' => 'Serv5 Digital Solutions', 'type' => 'text'],
    // ['key' => 'site_logo', 'value' => 'settings/logo.webp', 'type' => 'image', 'value_source' => 'file'],
    ['key' => 'company_video_url', 'value' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'type' => 'video', 'value_source' => 'link'],
    ['key' => 'hero_video_button_text', 'value' => 'Know more', 'type' => 'text'],
    
    // Hero (Disabled to preserve user design)
    // ['key' => 'hero_title_1', 'value' => 'Building The Future', 'type' => 'text'],
    // ['key' => 'hero_title_2', 'value' => 'Of Your Business', 'type' => 'text'],
    // ['key' => 'hero_desc', 'value' => 'We provide state-of-the-art web solutions, from stunning designs to powerful backend systems, helping you dominate the digital galaxy.', 'type' => 'text'],
    // ['key' => 'hero_cta_text', 'value' => 'Launch Your Project', 'type' => 'text'],
    // ['key' => 'hero_planet', 'value' => 'settings/hero_planet.webp', 'type' => 'image', 'value_source' => 'file'],
    
    // Footer
    ['key' => 'footer_heading_en', 'value' => "Ready to Conquer the Web?", 'type' => 'text'],
    ['key' => 'footer_heading_ar', 'value' => "هل أنت جاهز لغزو الويب؟", 'type' => 'text'],
    ['key' => 'footer_description_en', 'value' => 'Serv5 is your ultimate partner for digital transformation. We blend creativity with technology to build exceptional experiences.', 'type' => 'text'],
    ['key' => 'footer_description_ar', 'value' => 'شركة Serv5 هي شريكك الأمثل للتحول الرقمي. نحن نمزج الإبداع مع التكنولوجيا لبناء تجارب استثنائية.', 'type' => 'text'],
    ['key' => 'footer_button_text_en', 'value' => 'Get a Quote', 'type' => 'text'],
    ['key' => 'footer_button_text_ar', 'value' => 'اطلب تسعيرة', 'type' => 'text'],
    ['key' => 'footer_copyright_en', 'value' => '2026 © Serv5. All Rights Reserved. Designed with passion.', 'type' => 'text'],
    ['key' => 'footer_copyright_ar', 'value' => '٢٠٢٦ © Serv5. جميع الحقوق محفوظة. صُمم بشغف.', 'type' => 'text'],
    
    // Contact
    ['key' => 'contact_email', 'value' => 'info@serv5.com', 'type' => 'text'],
    ['key' => 'contact_phone', 'value' => '+20 123 456 7890', 'type' => 'text'],
    ['key' => 'contact_address', 'value' => 'Cairo, Egypt - Digital Innovation Hub', 'type' => 'text'],
];

foreach ($settings as $s) {
    Setting::updateOrCreate(['key' => $s['key']], $s);
}

// 2. Services
Service::truncate();
$services = [
    [
        'name' => ['en' => 'Web Development', 'ar' => 'تطوير المواقع'],
        'slug' => 'web-development',
        'description' => ['en' => 'Full-stack web applications built with Laravel, React, and Next.js for maximum performance.', 'ar' => 'تطبيقات ويب متكاملة مبنية بأحدث التقنيات لضمان أفضل أداء.'],
        'is_active' => true,
    ],
    [
        'name' => ['en' => 'UI/UX Design', 'ar' => 'تصميم واجهة المستخدم'],
        'slug' => 'ui-ux-design',
        'description' => ['en' => 'Creative and intuitive designs that provide a seamless user experience and stunning visuals.', 'ar' => 'تصاميم إبداعية توفر تجربة مستخدم سلسة ومرئيات مذهلة.'],
        'is_active' => true,
    ],
    [
        'name' => ['en' => 'Cloud Hosting', 'ar' => 'استضافة سحابية'],
        'slug' => 'cloud-hosting',
        'description' => ['en' => 'Reliable and scalable cloud hosting solutions to keep your business online 24/7.', 'ar' => 'حلول استضافة سحابية موثوقة وقابلة للتوسع لضمان بقاء عملك متاحاً دائماً.'],
        'is_active' => true,
    ],
    [
        'name' => ['en' => 'SEO Optimization', 'ar' => 'تحسين محركات البحث'],
        'slug' => 'seo-optimization',
        'description' => ['en' => 'Drive organic traffic to your site and rank higher on Google with our advanced SEO strategies.', 'ar' => 'اجذب الزوار لموقعك وتصدر نتائج البحث باستراتيجياتنا المتقدمة.'],
        'is_active' => true,
    ],
];
foreach ($services as $svc) {
    Service::create($svc);
}

// 3. Features
Feature::truncate();
$features = [
    [
        'title' => ['en' => 'Cutting-edge Tech', 'ar' => 'تقنيات متطورة'],
        'description' => ['en' => 'We use the latest frameworks like Laravel 12 and React 19.', 'ar' => 'نستخدم أحدث إطارات العمل مثل لارافيل 12 وريأكت 19.'],
        'icon' => 'heroicon-o-cpu-chip',
        'is_active' => true
    ],
    [
        'title' => ['en' => 'Fast Delivery', 'ar' => 'سرعة التسليم'],
        'description' => ['en' => 'Agile methodology ensuring your project launches on time.', 'ar' => 'منهجية العمل الرشيقة تضمن إطلاق مشروعك في الوقت المحدد.'],
        'icon' => 'heroicon-o-rocket-launch',
        'is_active' => true
    ],
    [
        'title' => ['en' => '24/7 Support', 'ar' => 'دعم على مدار الساعة'],
        'description' => ['en' => 'Our experts are always here to help you with any issues.', 'ar' => 'خبراؤنا متواجدون دائماً لمساعدتك في أي مشكلات.'],
        'icon' => 'heroicon-o-chat-bubble-left-right',
        'is_active' => true
    ],
];
foreach ($features as $f) {
    Feature::create($f);
}

// 4. Statistics
Statistic::truncate();
$stats = [
    ['label' => 'Projects Completed', 'count' => 250, 'icon' => 'heroicon-o-check-circle', 'is_active' => true],
    ['label' => 'Happy Clients', 'count' => 180, 'icon' => 'heroicon-o-user-group', 'is_active' => true],
    ['label' => 'Awards Won', 'count' => 15, 'icon' => 'heroicon-o-trophy', 'is_active' => true],
    ['label' => 'Years Experience', 'count' => 8, 'icon' => 'heroicon-o-clock', 'is_active' => true],
];
foreach ($stats as $s) {
    Statistic::create($s);
}

// 5. Social Links
SocialLink::truncate();
$socials = [
    ['platform' => 'facebook', 'url' => 'https://facebook.com/serv5', 'is_active' => true],
    ['platform' => 'twitter', 'url' => 'https://twitter.com/serv5', 'is_active' => true],
    ['platform' => 'linkedin', 'url' => 'https://linkedin.com/company/serv5', 'is_active' => true],
    ['platform' => 'instagram', 'url' => 'https://instagram.com/serv5', 'is_active' => true],
];
foreach ($socials as $s) {
    SocialLink::create($s);
}

// 6. Navigation Links
NavLink::truncate();
$navs = [
    ['label' => ['en' => 'Home', 'ar' => 'الرئيسية'], 'url' => '/', 'sort_order' => 1, 'is_active' => true],
    ['label' => ['en' => 'Services', 'ar' => 'الخدمات'], 'url' => '#', 'sort_order' => 2, 'is_active' => true],
    ['label' => ['en' => 'Portfolio', 'ar' => 'أعمالنا'], 'url' => '/portfolio', 'sort_order' => 3, 'is_active' => true],
    ['label' => ['en' => 'Blog', 'ar' => 'المدونة'], 'url' => '/blog', 'sort_order' => 4, 'is_active' => true],
    ['label' => ['en' => 'About', 'ar' => 'من نحن'], 'url' => '/about-us', 'sort_order' => 5, 'is_active' => true],
    ['label' => ['en' => 'Contact', 'ar' => 'اتصل بنا'], 'url' => '/contact-us', 'sort_order' => 6, 'is_active' => true],
];

foreach ($navs as $n) {
    $parent = NavLink::create($n);
    
    if ($n['label']['en'] === 'Services') {
        $children = [
            ['label' => ['en' => 'Web Development', 'ar' => 'برمجة المواقع'], 'url' => '/services-web-dev', 'sort_order' => 1],
            ['label' => ['en' => 'Web Design', 'ar' => 'تصميم المواقع'], 'url' => '/services-web-design', 'sort_order' => 2],
            ['label' => ['en' => 'Social Media', 'ar' => 'سوشيال ميديا'], 'url' => '/services-social-media', 'sort_order' => 3],
            ['label' => ['en' => 'Web Hosting', 'ar' => 'استضافة المواقع'], 'url' => '/services-web-hosting', 'sort_order' => 4],
        ];
        foreach ($children as $child) {
            NavLink::create(array_merge($child, [
                'parent_id' => $parent->id,
                'is_active' => true,
            ]));
        }
    }
}

DB::statement('SET FOREIGN_KEY_CHECKS=1;');
echo "Realistic data seeded successfully!";