<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;
use Illuminate\Support\Facades\Schema;

class ProjectSeeder extends Seeder
{
    public function run()
    {
        // Clear existing projects to prevent duplicates
        Schema::disableForeignKeyConstraints();
        Project::truncate();
        Schema::enableForeignKeyConstraints();

        $projects = [
            // --- WEBSITES ---
            [
                'name' => ['en' => 'SaaS Enterprise ERP', 'ar' => 'نظام تخطيط موارد المؤسسات ERP'],
                'category' => 'websites',
                'description' => ['en' => 'A comprehensive cloud-based ERP solution for managing sales, inventory, and human resources with advanced analytics.', 'ar' => 'حل سحابي شامل لإدارة المبيعات والمخازن والموارد البشرية مع تحليلات متقدمة ولوحة تحكم ذكية.'],
                'client' => 'Al-Futtaim Group',
                'url' => 'https://example.com',
                'tags' => ['Laravel', 'React', 'Tailwind CSS'],
                'type' => 'portfolio',
                'image' => '/portfolio/image-1.png',
            ],
            [
                'name' => ['en' => 'Real Estate Listing Hub', 'ar' => 'منصة عقارات دوت كوم'],
                'category' => 'websites',
                'description' => ['en' => 'A premium property listing platform featuring map search, virtual tours, and mortgage calculators for buyers and brokers.', 'ar' => 'منصة متكاملة لعرض وإدارة العقارات تتميز بالبحث الذكي على الخريطة والجولات الافتراضية وحساب القروض.'],
                'client' => 'Diyar Real Estate',
                'url' => 'https://example.com',
                'tags' => ['React', 'Node.js', 'Google Maps API', 'MongoDB'],
                'type' => 'portfolio',
                'image' => '/portfolio/image-2.png',
            ],
            [
                'name' => ['en' => 'Educational Academy LMS', 'ar' => 'أكاديمية التعليم الذكي LMS'],
                'category' => 'websites',
                'description' => ['en' => 'An interactive learning management system supporting live classrooms, online exams, and certification workflows.', 'ar' => 'منصة تعليم تفاعلية تدعم البث المباشر للمحاضرات، الاختبارات الإلكترونية وإصدار الشهادات التلقائية.'],
                'client' => 'Rowad Academy',
                'url' => 'https://example.com',
                'tags' => ['Vue.js', 'Laravel', 'WebRTC', 'MySQL'],
                'type' => 'portfolio',
                'image' => '/portfolio/image-3.png',
            ],

            // --- MOBILE APPS ---
            [
                'name' => ['en' => 'QuickDelivery Rider App', 'ar' => 'تطبيق كويك ديليفري للمندوبين'],
                'category' => 'mobile',
                'description' => ['en' => 'On-demand delivery mobile application with real-time routing, customer notifications, and secure payment processing.', 'ar' => 'تطبيق توصيل سريع يدعم تتبع خط السير الحي للمناديب، التنبيهات الفورية وبوابات الدفع الآمنة.'],
                'client' => 'QuickExpress',
                'url' => 'https://example.com',
                'tags' => ['Flutter', 'Firebase', 'Google Maps SDK', 'Stripe'],
                'type' => 'portfolio',
                'image' => '/portfolio/image-4.png',
            ],
            [
                'name' => ['en' => 'MedConsult Telehealth', 'ar' => 'تطبيق استشارتي الطبية'],
                'category' => 'mobile',
                'description' => ['en' => 'A secure telehealth application connecting patients with certified doctors via video consultation and e-prescriptions.', 'ar' => 'تطبيق رعاية صحية يتيح للمرضى حجز الاستشارات الطبية عبر الفيديو مع الأطباء المعتمدين والوصفات الإلكترونية.'],
                'client' => 'Saudi Health Ministry',
                'url' => 'https://example.com',
                'tags' => ['React Native', 'WebRTC', 'Node.js', 'AWS'],
                'type' => 'portfolio',
                'image' => '/portfolio/image-5.png',
            ],

            // --- MOTION GRAPHICS ---
            [
                'name' => ['en' => 'Brand Launch Explainer', 'ar' => 'فيديو موشن تعريفي للهوية'],
                'category' => 'motion',
                'description' => ['en' => 'An engaging 2D animation explainer video introducing the new corporate brand identity and target vision.', 'ar' => 'فيديو رسوم متحركة ثنائي الأبعاد 2D مشوق يعرف بالهوية البصرية الجديدة للمؤسسة ورؤيتها المستقبلية.'],
                'client' => 'Serv5 Solutions',
                'url' => 'https://example.com',
                'tags' => ['After Effects', 'Illustrator', '2D Animation'],
                'type' => 'portfolio',
                'image' => '/portfolio/image-6.png',
            ],
            [
                'name' => ['en' => 'Cybersecurity Awareness Video', 'ar' => 'فيديو توعية بالأمن السيبراني'],
                'category' => 'motion',
                'description' => ['en' => 'Animated educational short video illustrating common online threats and best safety practices for employees.', 'ar' => 'فيديو موشن جرافيك كرتوني توعوي يوضح التهديدات الأمنية الشائعة وطرق الوقاية منها للموظفين.'],
                'client' => 'SDAIA',
                'url' => 'https://example.com',
                'tags' => ['After Effects', 'Cinema 4D', 'Sound Design'],
                'type' => 'portfolio',
                'image' => '/portfolio/image-7.png',
            ],

            // --- DIGITAL IDENTITY ---
            [
                'name' => ['en' => 'Vanguard Brand Guidelines', 'ar' => 'دليل الهوية البصرية لفانغارد'],
                'category' => 'digital',
                'description' => ['en' => 'Complete corporate identity design including logo development, typography standards, and color palettes.', 'ar' => 'تصميم هوية بصرية متكاملة للشركة تشمل تصميم الشعار، اختيار الخطوط الرسمية وتنسيقات الألوان.'],
                'client' => 'Vanguard Holding',
                'url' => 'https://example.com',
                'tags' => ['Adobe Illustrator', 'Branding Book', 'Visual Design'],
                'type' => 'portfolio',
                'image' => '/portfolio/image-8.png',
            ],
            [
                'name' => ['en' => 'EcoGreens Sustainable Packaging', 'ar' => 'تصميم تغليف المنتجات المستدامة'],
                'category' => 'digital',
                'description' => ['en' => 'Eco-friendly product packaging and label designs aligned with the brands green vision.', 'ar' => 'تصميم عبوات وأغلفة منتجات صديقة للبيئة تعبر عن الرؤية الخضراء المستدامة للعلامة التجارية.'],
                'client' => 'EcoGreens Ltd',
                'url' => 'https://example.com',
                'tags' => ['Packaging Design', 'Photoshop', '3D Mockup'],
                'type' => 'portfolio',
                'image' => '/portfolio/image-9.png',
            ],

            // --- SOCIAL MEDIA ---
            [
                'name' => ['en' => 'Ramadan Campaign Visuals', 'ar' => 'تصاميم حملة رمضان المبارك'],
                'category' => 'social',
                'description' => ['en' => 'A set of cohesive social media posts and banners celebrating Ramadan with Islamic motifs and sleek typography.', 'ar' => 'مجموعة تصاميم متناسقة لوسائل التواصل الاجتماعي والبنرات الاحتفالية بشهر رمضان المبارك.'],
                'client' => 'STC Saudi',
                'url' => 'https://example.com',
                'tags' => ['Social Media Kit', 'Photoshop', 'Marketing Graphics'],
                'type' => 'portfolio',
                'image' => '/portfolio/image-3.png',
            ],
            [
                'name' => ['en' => 'Tech Summit Promotional Kit', 'ar' => 'حزمة منشورات القمة التقنية'],
                'category' => 'social',
                'description' => ['en' => 'Dynamic social media templates, countdown posts, and speaker profiles for a major tech summit.', 'ar' => 'قوالب تصاميم ديناميكية، منشورات العد التنازلي وبطاقات المتحدثين للترويج للقمة التقنية الكبرى.'],
                'client' => 'Digital Riyadh',
                'url' => 'https://example.com',
                'tags' => ['Social Media Kit', 'Figma', 'Promotional Design'],
                'type' => 'portfolio',
                'image' => '/portfolio/image-4.png',
            ],
        ];

        foreach ($projects as $project) {
            Project::create($project);
        }
    }
}
