<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ProjectSeeder extends Seeder
{
    public function run()
    {
        // Clear existing projects to prevent duplicates
        Schema::disableForeignKeyConstraints();
        Project::truncate();
        Schema::enableForeignKeyConstraints();

        // --- Local relative paths for storage ---
        $images = [
            'websites' => [
                'projects/image-1.webp',
                'projects/image-2.webp',
                'projects/image-3.webp',
            ],
            'mobile' => [
                'projects/image-4.webp',
                'projects/image-5.webp',
                'projects/image-6.webp',
            ],
            'motion' => [
                'projects/image-7.webp',
                'projects/image-8.webp',
                'projects/image-9.webp',
            ],
            'digital' => [
                'projects/image-1.webp',
                'projects/image-2.webp',
                'projects/image-3.webp',
            ],
            'social' => [
                'projects/image-4.webp',
                'projects/image-5.webp',
                'projects/image-6.webp',
            ],
        ];

        // --- Vocabulary for Random Generation ---
        $vocab = [
            'websites' => [
                'names' => ['Enterprise ERP Cloud', 'Real Estate Listing Hub', 'Educational LMS Academy', 'Fintech Capital Portal', 'Eco-Commerce Hub', 'Corporate Digital Portal'],
                'names_ar' => ['منصة ERP السحابية للمؤسسات', 'منصة عقارات دوت كوم', 'أكاديمية التعليم الذكي LMS', 'بوابة فينتك المالية', 'منصة التجارة الخضراء المستدامة', 'البوابة الرقمية للشركات'],
                'desc' => ['A highly scalable web portal featuring real-time analytics, dynamic widgets, and secure multi-tenant architectures.', 'An immersive custom web application designed to simplify workflows, boost conversions, and elevate user engagement.'],
                'desc_ar' => ['بوابة ويب قابلة للتوسع بشكل كبير تتميز بتحليلات فورية وأدوات تفاعلية وبنية متعددة الشركات آمنة.', 'تطبيق ويب مخصص وتفاعلي مصمم لتبسيط سير العمل وزيادة نسب التحويل وتعزيز تفاعل المستخدمين.'],
                'tags' => ['React', 'Next.js', 'Tailwind CSS', 'Laravel', 'MySQL', 'MongoDB'],
            ],
            'mobile' => [
                'names' => ['QuickDelivery Express Rider', 'MedConsult Telehealth App', 'FitLife Fitness Companion', 'Smart Wallet Pay', 'EcoShop Mobile App', 'EventPulse Booking Helper'],
                'names_ar' => ['تطبيق كويك ديليفري للمندوبين', 'تطبيق استشارتي الطبية للرعاية', 'تطبيق فت لايف للياقة البدنية', 'محفظة الدفع الذكية سمارت باي', 'تطبيق متجر إيكو شوب للهواتف', 'تطبيق حجز تذاكر إيفنت بلس'],
                'desc' => ['A native iOS and Android application with real-time mapping, biometric security, and premium interactive gestures.', 'A highly responsive multiplatform app connecting users to vital services with seamless user flows and offline storage.'],
                'desc_ar' => ['تطبيق أصيل لأنظمة iOS وأندرويد يتميز بخرائط تفاعلية فورية وحماية البصمة وإيماءات تفاعلية مميزة.', 'تطبيق متعدد الأنظمة سريع الاستجابة يربط المستخدمين بالخدمات الحيوية مع تدفقات مستخدم سلسة وتخزين بدون إنترنت.'],
                'tags' => ['Flutter', 'React Native', 'Firebase', 'iOS SDK', 'Android SDK', 'Stripe'],
            ],
            'motion' => [
                'names' => ['Brand Launch Explainer', 'Cybersecurity Awareness Video', 'Product Feature Demo', 'Isometric Architecture Tour', 'Animated Interface Intro', 'Global Summit Promo'],
                'names_ar' => ['فيديو موشن تعريفي للهوية', 'فيديو توعية بالأمن السيبراني', 'فيديو استعراض ميزات المنتج', 'جولة الهندسة المعمارية أيزومتريك', 'مقدمة متحركة لواجهة المستخدم', 'برومو القمة التقنية العالمية'],
                'desc' => ['An engaging 2D/3D motion graphics animation with professional voiceover, custom vectors, and cinematic sound design.', 'A high-impact visual explanation video showcasing product benefits through smooth transitions and stylized character animations.'],
                'desc_ar' => ['رسوم متحركة موشن جرافيك 2D/3D ممتعة مع تعليق صوتي احترافي ومتجهات مخصصة وتصميم صوت سينمائي.', 'فيديو شرح مرئي عالي التأثير يستعرض مزايا المنتج من خلال انتقالات سلسة ورسوم كرتونية مميزة للشخصيات.'],
                'tags' => ['After Effects', 'Cinema 4D', 'Illustrator', 'Sound Design', '2D Animation', 'Premiere Pro'],
            ],
            'digital' => [
                'names' => ['Vanguard Brand Guidelines', 'EcoGreens Packaging Suite', 'OmniCorp Visual Identity', 'Apex Typography & Logo', 'Summit Conference Identity', 'Horizon Rebranding Book'],
                'names_ar' => ['دليل الهوية البصرية لفانغارد', 'دليل تغليف وتعبئة إيكو جرينز', 'الهوية البصرية المتكاملة لأومني كورب', 'تصميم خط وشعار شركة أبكس', 'الهوية الرقمية لمؤتمر القمة التقني', 'كتاب إعادة بناء الهوية لهورايزون'],
                'desc' => ['A comprehensive brand identity design including modern vector logo development, typography systems, and color palettes.', 'A premium visual identity kit designed to set standards in packaging, digital footprint, and brand recognition.'],
                'desc_ar' => ['تصميم هوية بصرية متكاملة للعلامة التجارية يشمل تطوير الشعار المتجهي، أنظمة الخطوط وتنسيقات الألوان الرسمية.', 'حزمة الهوية المرئية المتميزة المصممة لوضع معايير في التعبئة والتغليف، البصمة الرقمية والوعي بالعلامة التجارية.'],
                'tags' => ['Figma', 'Photoshop', 'Illustrator', 'Branding Book', 'Visual Design', 'Typography'],
            ],
            'social' => [
                'names' => ['Ramadan Campaign Visuals', 'Tech Summit Promotional Kit', 'Black Friday Retail Banners', 'New Year Countdown Graphics', 'Client Testimonial Templates', 'Founders Day Post Pack'],
                'names_ar' => ['تصاميم حملة رمضان المبارك', 'حزمة منشورات القمة التقنية', 'بنرات مبيعات الجمعة البيضاء', 'منشورات العد التنازلي للعام الجديد', 'قوالب استعراض آراء العملاء', 'مجموعة منشورات يوم التأسيس الرسمي'],
                'desc' => ['A set of creative social media templates optimized for high engagement, pixel-perfect layout sizes, and uniform theme aesthetics.', 'Visual assets and design templates tailored to drive conversion, boost organic outreach, and establish brand voice on social feeds.'],
                'desc_ar' => ['مجموعة قوالب إبداعية لشبكات التواصل الاجتماعي محسنة لزيادة التفاعل، بأبعاد دقيقة لجميع المنصات وبجماليات متناسقة.', 'أصول مرئية وقوالب تصميم مخصصة لزيادة المبيعات، تعزيز الوصول العضوي وترسيخ صوت العلامة التجارية في منصات التواصل.'],
                'tags' => ['Figma', 'Photoshop', 'Social Media Kit', 'Visual Design', 'Marketing Graphics'],
            ],
        ];

        $clients = ['Al-Futtaim Group', 'STC Saudi', 'Saudi Health Ministry', 'Diyar Real Estate', 'SDAIA', 'EcoGreens Ltd', 'Digital Riyadh', 'Vanguard Holding'];
        $categoriesKeys = array_keys($vocab);
        $totalProjects = 500;

        for ($i = 1; $i <= $totalProjects; $i++) {
            $catKey = $categoriesKeys[$i % count($categoriesKeys)];
            
            $names = $vocab[$catKey]['names'];
            $namesAr = $vocab[$catKey]['names_ar'];
            $descs = $vocab[$catKey]['desc'];
            $descsAr = $vocab[$catKey]['desc_ar'];
            $tagsPool = $vocab[$catKey]['tags'];

            $nameEn = $names[array_rand($names)] . " ($i)";
            $nameAr = $namesAr[array_rand($namesAr)] . " ($i)";

            $descEn = $descs[array_rand($descs)];
            $descAr = $descsAr[array_rand($descsAr)];

            $randomImages = $images[$catKey];
            $image = $randomImages[array_rand($randomImages)];

            // Select 2-3 random tags
            shuffle($tagsPool);
            $tags = array_slice($tagsPool, 0, rand(2, 3));

            Project::create([
                'name' => ['en' => $nameEn, 'ar' => $nameAr],
                'slug' => Str::slug($nameEn),
                'category' => $catKey,
                'description' => ['en' => $descEn, 'ar' => $descAr],
                'client' => $clients[array_rand($clients)],
                'url' => 'https://example.com',
                'tags' => $tags,
                'type' => 'portfolio',
                'image' => $image,
            ]);
        }
    }
}
