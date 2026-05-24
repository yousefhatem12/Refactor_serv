<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class FullArabicSeeder extends Seeder
{
    // Helper: puts the same Arabic text in both 'en' and 'ar' keys
    private function t($text) { return ['en' => $text, 'ar' => $text]; }

    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        // ── NAV LINKS ──
        \App\Models\NavLink::truncate();
        $home = \App\Models\NavLink::create(['url' => '/Home', 'label' => $this->t('الرئيسية'), 'sort_order' => 1]);
        $aboutParent = \App\Models\NavLink::create(['url' => '#', 'label' => $this->t('من نحن'), 'sort_order' => 2]);
        \App\Models\NavLink::create(['url' => '/about-us', 'label' => $this->t('من نحن'), 'parent_id' => $aboutParent->id, 'sort_order' => 1]);
        \App\Models\NavLink::create(['url' => '/bank-accounts', 'label' => $this->t('الحسابات البنكية'), 'parent_id' => $aboutParent->id, 'sort_order' => 2]);
        $services = \App\Models\NavLink::create(['url' => '#', 'label' => $this->t('خدماتنا'), 'sort_order' => 3]);
        \App\Models\NavLink::create(['url' => '/portfolio', 'label' => $this->t('أعمالنا'), 'sort_order' => 4]);
        \App\Models\NavLink::create(['url' => '/blog', 'label' => $this->t('المدونة'), 'sort_order' => 5]);
        \App\Models\NavLink::create(['url' => '/our-products', 'label' => $this->t('منتجاتنا'), 'sort_order' => 6]);
        \App\Models\NavLink::create(['url' => '/careers', 'label' => $this->t('الوظائف'), 'sort_order' => 7]);
        \App\Models\NavLink::create(['url' => '/faq', 'label' => $this->t('الأسئلة الشائعة'), 'sort_order' => 8]);
        \App\Models\NavLink::create(['url' => '/contact-us', 'label' => $this->t('اتصل بنا'), 'sort_order' => 9]);

        // ── PROJECT CATEGORIES ──
        \App\Models\ProjectCategory::truncate();
        \App\Models\ProjectCategory::create(['slug' => 'websites', 'name' => $this->t('المواقع وتطبيقات الويب')]);
        \App\Models\ProjectCategory::create(['slug' => 'mobile', 'name' => $this->t('تطبيقات الجوال')]);
        \App\Models\ProjectCategory::create(['slug' => 'digital', 'name' => $this->t('التسويق الرقمي والهوية')]);

        // ── SERVICES ──
        \App\Models\Service::truncate();
        $servicesList = [
            ['slug' => 'web-development', 'name' => 'تطوير المواقع', 'desc' => 'حلول مخصصة لتطوير مواقع الويب باستخدام أحدث التقنيات لبناء مواقع سريعة وقابلة للتوسع ومتجاوبة.'],
            ['slug' => 'ui-ux-design', 'name' => 'تصميم واجهة وتجربة المستخدم', 'desc' => 'خدمات تصميم احترافية لواجهة وتجربة المستخدم لإنشاء تجارب رقمية مذهلة وسهلة الاستخدام.'],
            ['slug' => 'cloud-hosting', 'name' => 'الاستضافة السحابية', 'desc' => 'خدمات استضافة سحابية موثوقة وسريعة مع ضمان عمل بنسبة 99.9% ودعم فني متاح على مدار الساعة.'],
            ['slug' => 'seo-optimization', 'name' => 'تحسين محركات البحث', 'desc' => 'خدمات تهيئة المواقع لمحركات البحث لزيادة ترتيب موقعك وجلب زوار مستهدفين بشكل طبيعي.'],
            ['slug' => 'cyber-security', 'name' => 'الأمن السيبراني', 'desc' => 'حلول أمنية شاملة لحماية عملك وموقعك من التهديدات الرقمية والاختراقات الأمنية.'],
            ['slug' => 'social-media', 'name' => 'التسويق عبر مواقع التواصل', 'desc' => 'إدارة حملات تسويقية استراتيجية لزيادة انتشار علامتك التجارية على جميع منصات التواصل الاجتماعي.'],
            ['slug' => 'ecommerce', 'name' => 'حلول التجارة الإلكترونية', 'desc' => 'إنشاء متاجر إلكترونية متكاملة مع بوابات الدفع الإلكتروني، إدارة المخزون، وواجهات مستخدم رائعة.'],
            ['slug' => 'mobile-apps', 'name' => 'تطوير تطبيقات الجوال', 'desc' => 'تطبيقات جوال مخصصة لأنظمة أندرويد و iOS بأداء فائق وتصميمات واجهة حديثة وسلسة.'],
            ['slug' => 'digital-identity', 'name' => 'الهوية الرقمية وبناء العلامة التجارية', 'desc' => 'تصميم الهوية التجارية الكاملة للمؤسسات والشركات، وتطوير الشعار والكتيبات واللوجو التعريفي.'],
            ['slug' => 'network-marketing', 'name' => 'التسويق الشبكي والأنظمة البرمجية', 'desc' => 'برمجة وتطوير منصات وأنظمة التسويق متعدد المستويات والشبكي بأعلى معايير الحماية والأمان.'],
        ];
        foreach ($servicesList as $s) {
            \App\Models\Service::create([
                'slug' => $s['slug'], 'name' => $this->t($s['name']),
                'description' => $this->t($s['desc']), 'is_active' => true,
                'sections' => ['blocks' => [
                    ['type' => 'hero', 'data' => ['title' => $this->t($s['name']), 'subtitle' => $this->t($s['desc'])]],
                    ['type' => 'features', 'data' => ['title' => $this->t('لماذا تختار خدمتنا لـ ' . $s['name'] . '؟'), 'items' => [
                        ['title' => $this->t('فريق من الخبراء'), 'desc' => $this->t('نمتلك فريقاً من المتخصصين المعتمدين لتقديم نتائج بأعلى جودة.')],
                        ['title' => $this->t('التسليم في الوقت المحدد'), 'desc' => $this->t('نلتزم بتسليم المشاريع في المواعيد المحددة دون المساس بالجودة.')],
                        ['title' => $this->t('دعم متواصل'), 'desc' => $this->t('دعم فني متاح على مدار الساعة للحفاظ على استقرار واستمرارية أعمالكم.')],
                        ['title' => $this->t('أسعار تنافسية'), 'desc' => $this->t('خطط أسعار مرنة ومناسبة مصممة لتناسب الشركات الناشئة والكبيرة.')],
                    ]]],
                    ['type' => 'how_we_work_timeline', 'data' => ['items' => [
                        ['title' => $this->t('تحليل المتطلبات'), 'desc' => $this->t("دراسة متطلبات عملك وأهداف المشروع بالتفصيل قبل البدء.")],
                        ['title' => $this->t('التخطيط والاستراتيجية'), 'desc' => $this->t("وضع خطة عمل متكاملة وهندسة برمجية واضحة للنظام.")],
                        ['title' => $this->t('التصميم والتطوير'), 'desc' => $this->t("بدء بناء وبرمجة الحل الخاص بك باستخدام أحدث التقنيات المتطورة.")],
                        ['title' => $this->t('الاختبار والطلق'), 'desc' => $this->t("إجراء اختبارات دقيقة للوظائف والأمان ثم إطلاق المشروع بنجاح.")],
                        ['title' => $this->t('الدعم والصيانة'), 'desc' => $this->t("توفير الدعم المستمر والتحديثات الدورية لضمان الكفاءة القصوى للعمل.")],
                    ]]],
                    ['type' => 'blogs', 'data' => []],
                ]],
            ]);
        }

        // ── PAGES ──
        \App\Models\Page::truncate();
        $pages = [
            ['slug' => 'home', 'title' => 'حلول برمجية مبتكرة', 'subtitle' => 'مصممة خصيصاً لاحتياجاتك. نحن نساعدك على بناء المستقبل الرقمي.'],
            ['slug' => 'about-us', 'title' => 'من نحن', 'subtitle' => 'تعرف أكثر على رحلتنا، فريقنا المتميز، ولماذا يجب عليك اختيارنا.'],
            ['slug' => 'contact-us', 'title' => 'اتصل بنا', 'subtitle' => 'تواصل معنا لأي استفسارات أو للحصول على الدعم الفني اللازم لعملك.'],
            ['slug' => 'portfolio', 'title' => 'معرض أعمالنا', 'subtitle' => 'استكشف أحدث مشاريعنا وقصص النجاح التي حققناها لعملائنا.'],
            ['slug' => 'faq', 'title' => 'الأسئلة الشائعة', 'subtitle' => 'اعثر على إجابات وافية للأسئلة الشائعة حول خدماتنا وحلولنا البرمجية.'],
            ['slug' => 'blog', 'title' => 'المدونة البرمجية', 'subtitle' => 'اكتشف المقالات، الدروس، ورؤى الخبراء في مجالات التكنولوجيا المختلفة.'],
        ];
        foreach ($pages as $p) {
            \App\Models\Page::create(['slug' => $p['slug'], 'title' => $this->t($p['title']), 'subtitle' => $this->t($p['subtitle']),
                'sections' => ['template' => $p['slug'] === 'about-us' ? 'about' : ($p['slug'] === 'contact-us' ? 'contact' : ($p['slug'] === 'portfolio' ? 'portfolio' : ($p['slug'] === 'faq' ? 'faq' : ($p['slug'] === 'blog' ? 'blog' : null))))]]);
        }

        // ── POSTS ──
        \App\Models\Post::truncate();
        $posts = [
            ['title' => 'أهم 10 اتجاهات في تصميم الويب لعام 2026', 'content' => 'اكتشف أحدث صيحات وتوجهات تصميم المواقع التي تشكل المشهد الرقمي في عام 2026. من الواجهات الذكية المدعومة بالذكاء الاصطناعي إلى التصميمات التفاعلية ثلاثية الأبعاد.', 'category' => 'التصميم'],
            ['title' => 'كيف تختار شركة الاستضافة المناسبة لموقعك', 'content' => 'يعد اختيار مزود الاستضافة المناسب أمراً ضرورياً لأداء موقعك واستقراره. تعرف على العوامل الأساسية التي يجب مراعاتها مثل سرعة الاستجابة، الأمان، وإمكانية الترقية.', 'category' => 'الاستضافة'],
            ['title' => 'الدليل الشامل لتحسين محركات البحث لعام 2026', 'content' => 'احترف تهيئة موقعك لمحركات البحث مع دليلنا الشامل. بدءاً من اختيار الكلمات المفتاحية المناسبة ووصولاً إلى التهيئة التقنية الشاملة لموقعك.', 'category' => 'التسويق'],
            ['title' => 'بناء منصات التجارة الإلكترونية عالية الأداء والتوسع', 'content' => 'تعرف على أفضل الممارسات لبناء متاجر إلكترونية قادرة على التعامل مع ملايين المعاملات اليومية بكفاءة عالية وسرعة استجابة فائقة.', 'category' => 'التطوير'],
            ['title' => 'أفضل الممارسات لتطوير تطبيقات الجوال بجودة عالية', 'content' => 'نصائح وإرشادات أساسية لتطوير تطبيقات جوال متميزة. من تصميم واجهة المستخدم الجذابة إلى تحسين سرعة التطبيق واستقرار الأداء.', 'category' => 'التطوير'],
            ['title' => 'أساسيات الأمن السيبراني لحماية الشركات والمؤسسات', 'content' => 'احمِ عملك وبيانات عملائك من التهديدات السيبرانية باستخدام هذه الممارسات الأمنية الهامة. تعرف على آليات التشفير وجدران الحماية وغيرها.', 'category' => 'الحماية'],
        ];
        foreach ($posts as $i => $p) {
            \App\Models\Post::create(['title' => $this->t($p['title']), 'slug' => \Illuminate\Support\Str::slug($p['title']),
                'content' => $this->t($p['content']), 'category' => $p['category'],
                'is_published' => true, 'is_trending' => $i < 3, 'author_id' => 1]);
        }

        // ── FAQS ──
        \App\Models\Faq::truncate();
        $faqs = [
            ['q' => 'ما هي الخدمات التي تقدمها شركة Serv5؟', 'a' => 'نحن نقدم خدمات تطوير المواقع، تطبيقات الجوال، تصميم واجهات المستخدم، الاستضافة السحابية، تحسين محركات البحث، التسويق عبر وسائل التواصل، حلول التجارة الإلكترونية، الأمن السيبراني، وتصميم الهوية الرقمية.'],
            ['q' => 'كم من الوقت يستغرق تطوير المشروع؟', 'a' => 'تختلف المدة بناءً على حجم المشروع ومتطلباته. المواقع التعريفية البسيطة تستغرق من أسبوعين إلى 4 أسابيع، بينما تتطلب المشاريع والأنظمة الكبرى من شهرين إلى 6 أشهر، وسنقوم بتوفير جدول زمني مفصل.'],
            ['q' => 'هل توفرون الدعم الفني بعد تسليم المشروع؟', 'a' => 'نعم، نحن نوفر باقات صيانة ودعم فني متكاملة بعد إطلاق المشروع. فريق الدعم متوفر لمساعدتك في أي وقت لحل المشاكل التقنية وتثبيت التحديثات.'],
            ['q' => 'ما هي التقنيات البرمجية التي تعتمدون عليها؟', 'a' => 'نحن نعتمد على أحدث التقنيات بما في ذلك React، Next.js، Laravel، Node.js، Flutter، Swift، Kotlin، والخدمات السحابية من AWS. نختار التقنيات الأنسب لمشروعك.'],
            ['q' => 'كم تكلفة إنشاء مشروع أو تطبيق؟', 'a' => 'تعتمد التكلفة على نطاق العمل والمميزات المطلوبة للمشروع. نحن نقدم عروض أسعار تنافسية ومفصلة بعد مناقشة تفاصيل مشروعك وفهمه جيداً. اتصل بنا للحصول على استشارة مجانية.'],
            ['q' => 'هل يمكنني رؤية نماذج من أعمالكم السابقة؟', 'a' => 'بالتأكيد! يمكنك زيارة صفحة أعمالنا (المعرض) في الموقع لاستكشاف المشاريع السابقة التي قمنا بتنفيذها لعملائنا في مختلف المجالات والقطاعات.'],
        ];
        foreach ($faqs as $f) {
            \App\Models\Faq::create(['question' => $this->t($f['q']), 'answer' => $this->t($f['a']), 'is_active' => true]);
        }

        // ── PRODUCTS ──
        \App\Models\Product::truncate();
        $products = [
            ['slug' => 'restaurant-pos', 'name' => 'نظام نقاط البيع للمطاعم', 'cat' => 'برمجيات الخدمة SaaS', 'title' => 'نظام متكامل لإدارة المطاعم ونقاط البيع', 'desc' => 'نظام كاشير ونقاط بيع متطور مخصص للمطاعم لإدارة الطلبات، شاشات المطبخ، الحسابات، وتقارير المبيعات الفورية والمخزون.'],
            ['slug' => 'crm-platform', 'name' => 'منصة إدارة علاقات العملاء CRM', 'cat' => 'برمجيات الخدمة SaaS', 'title' => 'نظام إدارة ومتابعة العملاء والزيارات', 'desc' => 'نظام شامل لإدارة علاقات العملاء ومتابعة الصفقات والمهام وإتمام عمليات البيع مع تقارير وإحصائيات ذكية وتنبيهات مستمرة.'],
            ['slug' => 'hr-management', 'name' => 'نظام إدارة الموارد البشرية HR', 'cat' => 'أنظمة الشركات والمؤسسات', 'title' => 'إدارة الموارد البشرية والرواتب حضور وانصراف', 'desc' => 'نظام متكامل لإدارة الموظفين والرواتب، الحضور والانصراف، الإجازات، والتقييمات الدورية لتسهيل العمل الداخلي للمؤسسة.'],
        ];
        foreach ($products as $p) {
            \App\Models\Product::create(['slug' => $p['slug'], 'name' => $this->t($p['name']), 'category' => $this->t($p['cat']),
                'title' => $this->t($p['title']), 'description' => $this->t($p['desc']), 'is_active' => true,
                'demo_link' => '#', 'order_link' => '/contact-us', 'tech_specs' => ['Laravel', 'React', 'MySQL', 'Redis'],
                'features' => ['en' => [
                    ['title' => 'تقارير ذكية ولوحة تحكم', 'desc' => 'لوحة تحكم فورية لتحليل أداء العمل ومعرفة الإحصائيات المهمة.'],
                    ['title' => 'دعم اللغتين العربية والانجليزية', 'desc' => 'واجهات مستخدم متكاملة تدعم اللغتين بشكل كامل وممتاز.'],
                    ['title' => 'نظام سحابي مرن وأمن', 'desc' => 'إمكانية الوصول لبياناتك بأمان تام من أي مكان وفي أي وقت.'],
                    ['title' => 'ربط وتكامل برمجيات API', 'desc' => 'ربط مباشر وسلس مع بوابات الدفع الإلكتروني والرسائل النصية.'],
                ], 'ar' => [
                    ['title' => 'تقارير ذكية ولوحة تحكم', 'desc' => 'لوحة تحكم فورية لتحليل أداء العمل ومعرفة الإحصائيات المهمة.'],
                    ['title' => 'دعم اللغتين العربية والانجليزية', 'desc' => 'واجهات مستخدم متكاملة تدعم اللغتين بشكل كامل وممتاز.'],
                    ['title' => 'نظام سحابي مرن وأمن', 'desc' => 'إمكانية الوصول لبياناتك بأمان تام من أي مكان وفي أي وقت.'],
                    ['title' => 'ربط وتكامل برمجيات API', 'desc' => 'ربط مباشر وسلس مع بوابات الدفع الإلكتروني والرسائل النصية.'],
                ]]]);
        }

        // ── CAREERS ──
        \App\Models\Career::truncate();
        $careers = [
            ['title' => 'مطور ويب متكامل أول (Senior Full Stack)', 'dept' => 'الهندسة البرمجية', 'loc' => 'المنصورة، مصر', 'sal' => '20,000 - 35,000 ج.م', 'desc' => 'نحن نبحث عن مطور ويب متكامل أول ذو خبرة للانضمام إلى فريق التطوير لدينا والمساهمة في بناء وتطوير أنظمتنا البرمجية.'],
            ['title' => 'مصمم واجهة وتجربة المستخدم UI/UX', 'dept' => 'التصميم الإبداعي', 'loc' => 'عن بعد', 'sal' => '15,000 - 25,000 ج.م', 'desc' => 'انضم إلى فريق التصميم لإنشاء واجهات وتجارب استخدام تفاعلية ومميزة لمواقع الويب وتطبيقات الهواتف الذكية.'],
            ['title' => 'أخصائي تسويق رقمي وإعلانات ممولة', 'dept' => 'التسويق والمبيعات', 'loc' => 'الرياض، السعودية', 'sal' => '4,000 - 7,000 ريال', 'desc' => 'مطلوب أخصائي تسويق رقمي متميز لإدارة الحملات الإعلانية المدفوعة وتحسين ظهور الشركة في محركات البحث ومواقع التواصل.'],
        ];
        foreach ($careers as $c) {
            \App\Models\Career::create(['title' => $this->t($c['title']), 'department' => $c['dept'],
                'location' => $this->t($c['loc']), 'salary' => $this->t($c['sal']),
                'description' => $this->t($c['desc']), 'is_active' => true,
                'responsibilities' => ['en' => [['responsibility' => 'برمجة وتطوير التطبيقات بأعلى كفاءة وأمان'], ['responsibility' => 'التعاون مع فرق التصميم والتحليل لبناء الميزات الجديدة'], ['responsibility' => 'كتابة أكواد نظيفة وقابلة للتطوير والصيانة'], ['responsibility' => 'المشاركة في مراجعة الأكواد البرمجية والتوجيه التقني']],
                    'ar' => [['responsibility' => 'برمجة وتطوير التطبيقات بأعلى كفاءة وأمان'], ['responsibility' => 'التعاون مع فرق التصميم والتحليل لبناء الميزات الجديدة'], ['responsibility' => 'كتابة أكواد نظيفة وقابلة للتطوير والصيانة'], ['responsibility' => 'المشاركة في مراجعة الأكواد البرمجية والتوجيه التقني']]],
                'requirements' => ['en' => [['requirement' => 'خبرة عملية لا تقل عن 3 سنوات في المجال'], ['requirement' => 'مهارات ممتازة في حل المشكلات البرمجية والتحليل'], ['requirement' => 'القدرة على التواصل الفعال والعمل الجماعي مع الفريق'], ['requirement' => 'درجة البكالوريوس في علوم الحاسوب أو مجال ذي صلة']],
                    'ar' => [['requirement' => 'خبرة عملية لا تقل عن 3 سنوات في المجال'], ['requirement' => 'مهارات ممتازة في حل المشكلات البرمجية والتحليل'], ['requirement' => 'القدرة على التواصل الفعال والعمل الجماعي مع الفريق'], ['requirement' => 'درجة البكالوريوس في علوم الحاسوب أو مجال ذي صلة']]]]);
        }

        // ── BANK ACCOUNTS ──
        \App\Models\BankAccount::truncate();
        \App\Models\BankAccount::create(['name' => $this->t('البنك الأهلي المصري'), 'owner' => $this->t('شركة سرف فايف للتكنولوجيا'), 'account' => $this->t('0012345678901'), 'country' => 'egypt', 'type' => 'bank', 'brand_color' => 'from-[#1a472a] to-[#2d7a4a]', 'shadow' => 'rgba(26,71,42,0.4)', 'sort_order' => 1]);
        \App\Models\BankAccount::create(['name' => $this->t('مصرف الراجحي'), 'owner' => $this->t('شركة سرف فايف للتكنولوجيا'), 'account' => $this->t('SA0380000000608010167519'), 'country' => 'ksa', 'type' => 'bank', 'brand_color' => 'from-[#003366] to-[#005599]', 'shadow' => 'rgba(0,51,102,0.4)', 'sort_order' => 2]);
        \App\Models\BankAccount::create(['name' => $this->t('فودافون كاش'), 'owner' => $this->t('شركة سرف فايف للتكنولوجيا'), 'account' => $this->t('01012345678'), 'country' => 'egypt', 'type' => 'wallet', 'brand_color' => 'from-[#e60000] to-[#ff3333]', 'shadow' => 'rgba(230,0,0,0.4)', 'sort_order' => 3]);

        // ── PROJECTS ──
        \App\Models\Project::truncate();
        $projects = [
            ['name' => 'منصة التجارة الإلكترونية المتكاملة', 'cat' => 'websites', 'desc' => 'متجر إلكتروني حديث ومتكامل المميزات مع لوحة تحكم ذكية وبوابات دفع إلكتروني متعددة.', 'client' => 'شركة التقنية المحدودة', 'tags' => ['Laravel', 'React', 'Stripe']],
            ['name' => 'تطبيق الرعاية الصحية والهواتف', 'cat' => 'mobile', 'desc' => 'تطبيق متميز لحجز مواعيد الأطباء والاستشارات الطبية عن بعد بطريقة سهلة وسلسة.', 'client' => 'مستشفى الرعاية الطبية', 'tags' => ['Flutter', 'Firebase', 'Node.js']],
            ['name' => 'تصميم الهوية التجارية الكاملة', 'cat' => 'digital', 'desc' => 'تصميم وتطوير الهوية التجارية والشعار والبروفايل التعريفي للمؤسسات والشركات.', 'client' => 'حاضنة الأعمال والابتكار', 'tags' => ['Branding', 'Illustrator', 'Figma']],
            ['name' => 'بوابة العقارات وإدارة الأملاك', 'cat' => 'websites', 'desc' => 'موقع متكامل لعرض وطلب العقارات مع خرائط تفاعلية وجولات افتراضية داخل العقار.', 'client' => 'شركة الأملاك العقارية', 'tags' => ['Next.js', 'PostgreSQL', 'Google Maps']],
            ['name' => 'تطبيق طلب وتوصيل الطعام الفوري', 'cat' => 'mobile', 'desc' => 'تطبيق جوال لتوصيل الوجبات مع تتبع مباشر لمكان السائق على الخريطة وإشعارات لحظية.', 'client' => 'سلسلة مطاعم كويك بايت', 'tags' => ['React Native', 'Node.js', 'MongoDB']],
            ['name' => 'الحملة التسويقية وصناعة المحتوى', 'cat' => 'digital', 'desc' => 'إطلاق حملات إعلانية ممولة وكتابة وصناعة المحتوى الإبداعي لوسائل التواصل الاجتماعي.', 'client' => 'العلامة التجارية للموضة', 'tags' => ['Marketing', 'Content', 'Analytics']],
        ];
        foreach ($projects as $p) {
            \App\Models\Project::create(['name' => $this->t($p['name']), 'category' => $p['cat'],
                'description' => $this->t($p['desc']), 'client' => $p['client'],
                'url' => 'https://example.com', 'tags' => $p['tags'], 'type' => 'portfolio']);
        }

        // ── FOOTER LINKS ──
        \App\Models\FooterLink::truncate();
        $footer = [
            'useful_links' => ['صفحات الهبوط', 'منشئ النوافذ المنبثقة', 'تصميم المواقع', 'المحتوى الرقمي', 'الربط البرمجي والـ API'],
            'quick_links' => ['مصممو الويب', 'المسوقين', 'المشاريع الصغيرة', 'منشئ المواقع الإحترافي'],
            'offers' => ['الأكاديمية التعليمية', 'المدونة الرقمية', 'قوالب التصميم', 'خدمات الاستضافة', 'بوابة المطورين', 'الدعم الفني والتقني'],
            'company' => ['من نحن', 'فرص العمل والوظائف', 'الأسئلة الشائعة', 'فريق العمل الإبداعي', 'اتصل بنا والبريد الإلكتروني'],
        ];
        foreach ($footer as $col => $links) {
            foreach ($links as $i => $label) {
                \App\Models\FooterLink::create(['column_name' => $col, 'label' => $this->t($label),
                    'url' => '/' . strtolower(str_replace(' ', '-', $label)), 'sort_order' => $i]);
            }
        }

        // ── SETTINGS ──
        \App\Models\Setting::updateOrCreate(['key' => 'site_phone'], ['value' => '00201288863831', 'type' => 'text']);
        \App\Models\Setting::updateOrCreate(['key' => 'site_email'], ['value' => 'sales@serv5.com', 'type' => 'text']);
        \App\Models\Setting::updateOrCreate(['key' => 'site_address_en'], ['value' => 'مصر - المنصورة - شارع قناة السويس - أعلى صيدلية د. رامي', 'type' => 'text']);
        \App\Models\Setting::updateOrCreate(['key' => 'site_address_ar'], ['value' => 'مصر - المنصورة - شارع قناة السويس - أعلى صيدلية د. رامي', 'type' => 'text']);
        \App\Models\Setting::updateOrCreate(['key' => 'footer_heading'], ['value' => json_encode($this->t('امتلك تصميم ويب رائع<br/>مع شركة Serv5.')), 'type' => 'text']);
        \App\Models\Setting::updateOrCreate(['key' => 'footer_description'], ['value' => json_encode($this->t('تتخصص شركة Serv5 في تقديم الحلول البرمجية الحديثة وتصميم المواقع وتطبيقات الهواتف بجودة ممتازة.')), 'type' => 'text']);
        \App\Models\Setting::updateOrCreate(['key' => 'footer_button_text'], ['value' => json_encode($this->t('ابدأ معنا الآن')), 'type' => 'text']);
        \App\Models\Setting::updateOrCreate(['key' => 'footer_copyright'], ['value' => json_encode($this->t('2026 © جميع الحقوق محفوظة لشركة Serv5.')), 'type' => 'text']);

        // ── SOCIAL LINKS ──
        \App\Models\SocialLink::truncate();
        \App\Models\SocialLink::create(['platform' => 'facebook', 'url' => 'https://facebook.com/serv5', 'is_active' => true]);
        \App\Models\SocialLink::create(['platform' => 'twitter', 'url' => 'https://x.com/serv5', 'is_active' => true]);
        \App\Models\SocialLink::create(['platform' => 'instagram', 'url' => 'https://instagram.com/serv5', 'is_active' => true]);
        \App\Models\SocialLink::create(['platform' => 'linkedin', 'url' => 'https://linkedin.com/company/serv5', 'is_active' => true]);

        // ── TESTIMONIALS ──
        \App\Models\Testimonial::truncate();
        \App\Models\Testimonial::create(['client_name' => 'جيمس ويلسون', 'position' => 'الرئيس التنفيذي لشركة تيك سوليوشنز', 'review' => 'قدمت لنا Serv5 منصة تجارة إلكترونية استثنائية غيرت مجرى أعمالنا تماماً. كان فريقهم محترفاً للغاية ومتعاوناً وتجاوز توقعاتنا بكثير.', 'is_active' => true]);
        \App\Models\Testimonial::create(['client_name' => 'سارة جونسون', 'position' => 'مديرة التسويق في براند كو', 'review' => 'الاستراتيجية الإعلانية المتميزة والتسويق عبر وسائل التواصل التي صممتها Serv5 رفعت نسبة التفاعل لدينا بمعدل 300%. نتائج مذهلة فعلاً.', 'is_active' => true]);
        \App\Models\Testimonial::create(['client_name' => 'مايكل براون', 'position' => 'المدير التقني لتطبيق فاينانس آب', 'review' => 'كان العمل مع شركة Serv5 لتطوير تطبيق الهاتف الخاص بنا تجربة رائعة ومريحة. سلمونا تطبيقاً متكاملاً بجودة ممتازة في الوقت المحدد تماماً.', 'is_active' => true]);

        Schema::enableForeignKeyConstraints();
        $this->command->info('Full Arabic content seeded successfully!');
    }
}
