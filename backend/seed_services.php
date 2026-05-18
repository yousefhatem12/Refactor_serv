<?php

use App\Models\Service;

// Web Development
$webDev = Service::where('slug', 'web-development')->first();
if ($webDev) {
    $webDev->sections = [
        'show_hero' => true,
        'hero_title' => ['en' => 'Web Development', 'ar' => 'برمجة المواقع'],
        'hero_subtitle' => ['en' => 'We offer a very complete and integrated web development services.', 'ar' => 'نحن نقدم خدمات تطوير ويب متكاملة ومتكاملة تمامًا.'],
        'show_features' => true,
        'features_title' => ['en' => 'Web Design Features', 'ar' => 'مميزات تصميم المواقع'],
        'features_subtitle' => ['en' => 'A web design that by a simple modern and professional design...', 'ar' => 'تصميم ويب يتميز بتصميم حديث وبسيط واحترافي...'],
        'features_list' => [
            ['title' => ['en' => 'Professional Design', 'ar' => 'تصميم احترافي'], 'desc' => ['en' => 'Modern and professional design...', 'ar' => 'تصميم حديث واحترافي...']],
            ['title' => ['en' => 'Modern Colors', 'ar' => 'ألوان عصرية'], 'desc' => ['en' => 'Modern colors...', 'ar' => 'ألوان عصرية...']],
            ['title' => ['en' => 'Free Design', 'ar' => 'تصميم مجاني'], 'desc' => ['en' => 'Free design...', 'ar' => 'تصميم مجاني...']],
            ['title' => ['en' => 'Responsive Design', 'ar' => 'تصميم متجاوب'], 'desc' => ['en' => 'Responsive design...', 'ar' => 'تصميم متجاوب...']],
            ['title' => ['en' => 'Multi-lingual', 'ar' => 'متعدد اللغات'], 'desc' => ['en' => 'Multi-lingual...', 'ar' => 'متعدد اللغات...']],
            ['title' => ['en' => 'UI Components', 'ar' => 'مكونات واجهة المستخدم'], 'desc' => ['en' => 'UI components...', 'ar' => 'مكونات واجهة المستخدم...']],
        ],
        'show_how_we_work' => true,
        'how_we_work_list' => [
            ['title' => ['en' => 'Requirement Gathering', 'ar' => 'جمع المتطلبات'], 'desc' => ['en' => 'Gathering...', 'ar' => 'جمع...']],
            ['title' => ['en' => 'Design', 'ar' => 'التصميم'], 'desc' => ['en' => 'Design...', 'ar' => 'التصميم...']],
            ['title' => ['en' => 'Development', 'ar' => 'التطوير'], 'desc' => ['en' => 'Development...', 'ar' => 'التطوير...']],
            ['title' => ['en' => 'Testing', 'ar' => 'الاختبار'], 'desc' => ['en' => 'Testing...', 'ar' => 'الاختبار...']],
            ['title' => ['en' => 'Deployment', 'ar' => 'النشر'], 'desc' => ['en' => 'Deployment...', 'ar' => 'النشر...']],
        ],
        'show_portfolio' => true,
        'show_request' => true,
        'show_blogs' => true,
    ];
    $webDev->save();
}

// UI/UX Design
$uiUx = Service::where('slug', 'ui-ux-design')->first();
if ($uiUx) {
    $uiUx->sections = [
        'show_hero' => true,
        'hero_title' => ['en' => 'UI/UX Design', 'ar' => 'تصميم واجهات المستخدم'],
        'hero_subtitle' => ['en' => 'We create amazing UI/UX designs.', 'ar' => 'نحن نصنع تصميمات رائعة.'],
        'show_features' => true,
        'features_title' => ['en' => 'UI/UX Features', 'ar' => 'مميزات التصميم'],
        'features_subtitle' => ['en' => 'Best UI/UX features...', 'ar' => 'أفضل المميزات...'],
        'features_list' => [
            ['title' => ['en' => 'User Research', 'ar' => 'أبحاث المستخدم'], 'desc' => ['en' => 'User research...', 'ar' => 'أبحاث المستخدم...']],
            ['title' => ['en' => 'Wireframing', 'ar' => 'رسم الهياكل'], 'desc' => ['en' => 'Wireframing...', 'ar' => 'رسم الهياكل...']],
            ['title' => ['en' => 'Prototyping', 'ar' => 'النماذج الأولية'], 'desc' => ['en' => 'Prototyping...', 'ar' => 'النماذج الأولية...']],
        ],
        'show_how_we_work' => false,
        'show_portfolio' => true,
        'show_request' => true,
        'show_blogs' => true,
    ];
    $uiUx->save();
}

// App Development
$appDev = Service::where('slug', 'app-development')->first();
if ($appDev) {
    $appDev->sections = [
        'show_hero' => true,
        'hero_title' => ['en' => 'Mobile Application Development', 'ar' => 'تطوير تطبيقات الجوال'],
        'hero_subtitle' => ['en' => 'We build native and cross-platform apps.', 'ar' => 'نحن نبني تطبيقات جوال.'],
        'show_features' => true,
        'features_title' => ['en' => 'App Features', 'ar' => 'مميزات التطبيق'],
        'features_subtitle' => ['en' => 'App features description.', 'ar' => 'وصف مميزات التطبيق.'],
        'features_list' => [
            ['title' => ['en' => 'Native Apps', 'ar' => 'تطبيقات أصلية'], 'desc' => ['en' => 'Native iOS & Android...', 'ar' => 'أندرويد و iOS...']],
            ['title' => ['en' => 'Fast Performance', 'ar' => 'أداء سريع'], 'desc' => ['en' => 'Fast performance...', 'ar' => 'أداء سريع...']],
            ['title' => ['en' => 'Secure', 'ar' => 'آمن'], 'desc' => ['en' => 'Secure apps...', 'ar' => 'تطبيقات آمنة...']],
        ],
        'show_how_we_work' => true,
        'how_we_work_list' => [
            ['title' => ['en' => 'Design and Requirement analysis', 'ar' => 'تحليل وتصميم المتطلبات'], 'desc' => ['en' => "UI/UX design\nWeb Architecture\nCustomer review", 'ar' => "تصميم\nمراجعة"]],
            ['title' => ['en' => 'Development', 'ar' => 'التطوير والبرمجة'], 'desc' => ['en' => "Development...", 'ar' => "تطوير..."]],
        ],
        'show_portfolio' => true,
        'show_request' => true,
        'show_blogs' => true,
    ];
    $appDev->save();
}

// Cloud Hosting
$cloudHosting = Service::where('slug', 'cloud-hosting')->first();
if ($cloudHosting) {
    $cloudHosting->sections = [
        'show_hero' => true,
        'hero_title' => ['en' => 'Cloud Hosting', 'ar' => 'الاستضافة السحابية'],
        'hero_subtitle' => ['en' => 'Discover our powerful hosting services.', 'ar' => 'اكتشف خدمات الاستضافة القوية لدينا.'],
        'show_features' => true,
        'features_title' => ['en' => 'Hosting Features', 'ar' => 'مميزات الاستضافة'],
        'features_subtitle' => ['en' => 'Best hosting features.', 'ar' => 'أفضل مميزات الاستضافة.'],
        'features_list' => [
            ['title' => ['en' => '99.9% Uptime', 'ar' => 'وقت تشغيل 99.9%'], 'desc' => ['en' => 'Guaranteed uptime.', 'ar' => 'وقت تشغيل مضمون.']],
            ['title' => ['en' => 'Fast Speed', 'ar' => 'سرعة عالية'], 'desc' => ['en' => 'Blazing fast.', 'ar' => 'سرعة فائقة.']],
        ],
        'show_hosting_offers' => true,
        'hosting_offers_list' => [
            ['title' => ['en' => 'Basic Plan', 'ar' => 'الخطة الأساسية'], 'desc' => ['en' => 'For small sites.', 'ar' => 'للمواقع الصغيرة.']],
            ['title' => ['en' => 'Pro Plan', 'ar' => 'خطة المحترفين'], 'desc' => ['en' => 'For large sites.', 'ar' => 'للمواقع الكبيرة.']],
        ],
        'show_portfolio' => false,
        'show_request' => false,
        'show_blogs' => true,
    ];
    $cloudHosting->save();
}

// Social Media
$socialMedia = Service::where('slug', 'social-media')->first();
if ($socialMedia) {
    $socialMedia->sections = [
        'show_hero' => true,
        'hero_title' => ['en' => 'Social Media Management', 'ar' => 'إدارة وسائل التواصل الاجتماعي'],
        'hero_subtitle' => ['en' => 'Grow your online presence.', 'ar' => 'نمِّ تواجدك على الإنترنت.'],
        'show_digital_marketing' => true,
        'digital_marketing_title' => ['en' => 'Digital Marketing Services', 'ar' => 'خدمات التسويق الرقمي'],
        'digital_marketing_subtitle' => ['en' => 'Boost your traffic.', 'ar' => 'زيادة الزيارات.'],
        'digital_marketing_list' => [
            ['title' => ['en' => 'SEO', 'ar' => 'تحسين محركات البحث'], 'desc' => ['en' => 'Rank higher.', 'ar' => 'تصدر النتائج.']],
            ['title' => ['en' => 'PPC', 'ar' => 'إعلانات الدفع لكل نقرة'], 'desc' => ['en' => 'Paid ads.', 'ar' => 'إعلانات مدفوعة.']],
        ],
        'show_why_choose_us' => true,
        'why_choose_us_title' => ['en' => 'Why Choose Us', 'ar' => 'لماذا تختارنا'],
        'why_choose_us_subtitle' => ['en' => 'We are the best.', 'ar' => 'نحن الأفضل.'],
        'why_choose_us_list' => [
            ['title' => ['en' => 'Expert Team', 'ar' => 'فريق خبير'], 'desc' => ['en' => 'Experts in marketing.', 'ar' => 'خبراء في التسويق.']],
            ['title' => ['en' => 'Proven Results', 'ar' => 'نتائج مثبتة'], 'desc' => ['en' => 'Results that matter.', 'ar' => 'نتائج ملموسة.']],
        ],
        'show_how_we_work' => true,
        'how_we_work_list' => [
            ['title' => ['en' => 'Strategy', 'ar' => 'الاستراتيجية'], 'desc' => ['en' => 'Planning...', 'ar' => 'تخطيط...']],
            ['title' => ['en' => 'Execution', 'ar' => 'التنفيذ'], 'desc' => ['en' => 'Executing...', 'ar' => 'تنفيذ...']],
        ],
        'show_portfolio' => false,
        'show_request' => true,
        'show_blogs' => true,
    ];
    $socialMedia->save();
}

// SEO Optimization
$seo = Service::where('slug', 'seo-optimization')->first();
if ($seo) {
    $seo->sections = [
        'show_hero' => true,
        'hero_title' => ['en' => 'SEO Optimization', 'ar' => 'تحسين محركات البحث'],
        'hero_subtitle' => ['en' => 'Rank on the first page of Google.', 'ar' => 'تصدر الصفحة الأولى في جوجل.'],
        'show_digital_marketing' => true,
        'digital_marketing_title' => ['en' => 'SEO Services', 'ar' => 'خدمات السيو'],
        'digital_marketing_subtitle' => ['en' => 'Complete SEO services.', 'ar' => 'خدمات سيو متكاملة.'],
        'digital_marketing_list' => [
            ['title' => ['en' => 'On-Page SEO', 'ar' => 'السيو الداخلي'], 'desc' => ['en' => 'On-page optimization.', 'ar' => 'تحسين السيو الداخلي.']],
            ['title' => ['en' => 'Off-Page SEO', 'ar' => 'السيو الخارجي'], 'desc' => ['en' => 'Off-page optimization.', 'ar' => 'تحسين السيو الخارجي.']],
        ],
        'show_why_choose_us' => true,
        'why_choose_us_title' => ['en' => 'Why Serv5', 'ar' => 'لماذا سيرف فايف'],
        'why_choose_us_subtitle' => ['en' => 'We deliver results.', 'ar' => 'نحن نحقق النتائج.'],
        'why_choose_us_list' => [
            ['title' => ['en' => 'White Hat SEO', 'ar' => 'سيو آمن'], 'desc' => ['en' => 'Safe techniques.', 'ar' => 'تقنيات آمنة.']],
            ['title' => ['en' => 'Monthly Reports', 'ar' => 'تقارير شهرية'], 'desc' => ['en' => 'Detailed reports.', 'ar' => 'تقارير مفصلة.']],
        ],
        'show_how_we_work' => true,
        'how_we_work_list' => [
            ['title' => ['en' => 'Audit', 'ar' => 'فحص'], 'desc' => ['en' => 'Site audit.', 'ar' => 'فحص الموقع.']],
            ['title' => ['en' => 'Optimization', 'ar' => 'تحسين'], 'desc' => ['en' => 'Optimizing...', 'ar' => 'تحسين...']],
        ],
        'show_portfolio' => false,
        'show_request' => true,
        'show_blogs' => true,
    ];
    $seo->save();
}

echo "Seeded services successfully!\n";
