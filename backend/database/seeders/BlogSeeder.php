<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BlogCategory;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BlogSeeder extends Seeder
{
    public function run()
    {
        // Clear existing blog data
        Schema::disableForeignKeyConstraints();
        Post::truncate();
        BlogCategory::truncate();
        Schema::enableForeignKeyConstraints();

        // Get an author (Super Admin)
        $author = User::first();
        $authorId = $author ? $author->id : 1;

        // --- Create Blog Categories ---
        $categories = [
            'technology' => [
                'slug' => 'technology',
                'name' => ['en' => 'Technology', 'ar' => 'تكنولوجيا'],
            ],
            'design' => [
                'slug' => 'design',
                'name' => ['en' => 'Design & UI/UX', 'ar' => 'التصميم وواجهة المستخدم'],
            ],
            'development' => [
                'slug' => 'development',
                'name' => ['en' => 'Web Development', 'ar' => 'تطوير المواقع والبرمجة'],
            ],
            'marketing' => [
                'slug' => 'marketing',
                'name' => ['en' => 'Digital Marketing', 'ar' => 'التسويق الرقمي والسيو'],
            ],
        ];

        $categoryModels = [];
        foreach ($categories as $key => $catData) {
            $categoryModels[$key] = BlogCategory::create($catData);
        }

        // --- Local relative paths for storage ---
        $images = [
            'technology' => [
                'posts/Link → post-landscape-1.jpg.webp',
                'posts/Link → post-landscape-2.jpg.webp',
            ],
            'design' => [
                'posts/Link → post-landscape-3.jpg.webp',
                'posts/Link post-landscape-3.jpg.webp',
            ],
            'development' => [
                'posts/Linfgd post-landscape-6.jpg.webp',
                'posts/get-our-blog.webp',
            ],
            'marketing' => [
                'posts/Link → post-landscape-1.jpg.webp',
                'posts/Link → post-landscape-2.jpg.webp',
            ],
        ];

        // --- Vocabulary for Random Generation ---
        $vocab = [
            'technology' => [
                'topics' => ['Artificial Intelligence', 'Cybersecurity', 'Cloud Computing', 'Quantum Cryptography', 'Edge Analytics', 'Blockchain Nodes', 'SaaS Architectures', '5G Infrastructure', 'Internet of Things', 'Big Data Engineering'],
                'topics_ar' => ['الذكاء الاصطناعي', 'الأمن السيبراني', 'الحوسبة السحابية', 'التشفير الكمي', 'تحليلات الحافة', 'عقد البلوكشين', 'بنيات البرمجيات كخدمة', 'بنية الجيل الخامس 5G', 'إنترنت الأشياء', 'هندسة البيانات الضخمة'],
                'impact' => ['Revolutionizing Software Development', 'Enhancing Enterprise Efficiency', 'Empowering Global Communities', 'Securing Online Transactions', 'Optimizing Machine Learning Models', 'Redefining Digital Workspaces'],
                'impact_ar' => ['ثورة في تطوير البرمجيات', 'تعزيز كفاءة الشركات', 'تمكين المجتمعات العالمية', 'تأمين المعاملات عبر الإنترنت', 'تحسين نماذج تعلم الآلة', 'إعادة تعريف مساحات العمل الرقمية'],
            ],
            'design' => [
                'topics' => ['User Experience Design', 'Responsive Micro-interactions', 'Bento Grid UI Layouts', 'Glassmorphic Themes', 'Accessible Design Systems', 'Minimalist Aesthetics', 'Dynamic Page Transitions', 'Typography Hierarchies'],
                'topics_ar' => ['تصميم تجربة المستخدم', 'التفاعلات الدقيقة سريعة الاستجابة', 'تخطيطات واجهة شبكة بينتو', 'السمات الزجاجية', 'أنظمة التصميم سهلة الوصول', 'الجماليات البسيطة المينيماليزم', 'انتقالات الصفحات الديناميكية', 'التسلسل الهرمي للخطوط'],
                'impact' => ['Maximizing User Retention', 'Creating Premium Brand Impressions', 'Simplifying Interface Navigation', 'Elevating Customer Engagement', 'Optimizing Web Accessibility', 'Enhancing Interactive Experiences'],
                'impact_ar' => ['زيادة الاحتفاظ بالمستخدمين', 'إنشاء انطباع مميز للعلامة التجارية', 'تبسيط التنقل في الواجهة', 'رفع مستوى تفاعل العملاء', 'تحسين إمكانية الوصول إلى الويب', 'تعزيز التجارب التفاعلية'],
            ],
            'development' => [
                'topics' => ['Next.js App Routers', 'TypeScript Compilation', 'Prisma ORM Implementations', 'Laravel Filament Dashboards', 'Tailwind Utility Engines', 'NodeJS Cluster Pools', 'GraphQL Subscriptions', 'Dockerized Multi-Containers'],
                'topics_ar' => ['موجهات تطبيقات Next.js', 'تجميع لغة TypeScript', 'تطبيقات Prisma ORM', 'لوحات تحكم Laravel Filament', 'محركات أدوات Tailwind CSS', 'تجميع خوادم NodeJS Cluster', 'اشتراكات ومزامنة GraphQL', 'حاويات Docker المتعددة'],
                'impact' => ['Boosting Web Application Performance', 'Reducing Hydration Layout Shifts', 'Securing RESTful Web Services', 'Scaling Backend Node Servers', 'Enabling Fullstack Edge Deploys', 'Streamlining Git Workflows'],
                'impact_ar' => ['زيادة أداء تطبيقات الويب', 'تقليل تغيرات تخطيط الهيدريشن', 'تأمين خدمات الويب RESTful', 'توسيع خوادم العقد الخلفية', 'تمكين نشر خوادم الحافة المتكاملة', 'تبسيط عمليات Git وسير العمل'],
            ],
            'marketing' => [
                'topics' => ['Search Engine Optimization (SEO)', 'High-Converting Copywriting', 'Google Analytics 4 Tracking', 'Email Sequence Automations', 'Vibrant Content Creation', 'Brand Social Strategy', 'PPC Bid Strategies', 'Affiliate Revenue Funnels'],
                'topics_ar' => ['تحسين محركات البحث (SEO)', 'كتابة النصوص عالية التحويل', 'تتبع تحليلات جوجل GA4', 'أتمتة تسلسلات البريد الإلكتروني', 'إنشاء المحتوى الإبداعي النابض', 'استراتيجية العلامات التجارية للتواصل الاجتماعي', 'استراتيجيات عروض أسعار النقرات', 'أقماع إيرادات التسويق بالعمولة'],
                'impact' => ['Generating Organic Leads', 'Driving Targeted Traffic', 'Increasing ROI Performance', 'Building Sustainable Audience Engagement', 'Scaling Customer Lifespans', 'Unlocking Market Potential'],
                'impact_ar' => ['توليد عملاء محتملين بشكل طبيعي', 'توجيه الزيارات المستهدفة للموقع', 'زيادة عائد الاستثمار ROI', 'بناء تفاعل مستدام مع الجمهور', 'توسيع دورة حياة العميل', 'إطلاق العنان للإمكانيات الكاملة للماركت'],
            ],
        ];

        // Seed posts
        $categoryKeys = array_keys($categories);
        $totalPosts = 1000;

        $createdAtBase = Carbon::now()->subDays($totalPosts);

        for ($i = 1; $i <= $totalPosts; $i++) {
            $catKey = $categoryKeys[$i % count($categoryKeys)];
            $catModel = $categoryModels[$catKey];
            
            // Randomly select items from vocabulary
            $topics = $vocab[$catKey]['topics'];
            $topicsAr = $vocab[$catKey]['topics_ar'];
            $impacts = $vocab[$catKey]['impact'];
            $impactsAr = $vocab[$catKey]['impact_ar'];
            
            $topic = $topics[array_rand($topics)];
            $topicAr = $topicsAr[array_rand($topicsAr)];
            $impact = $impacts[array_rand($impacts)];
            $impactAr = $impactsAr[array_rand($impactsAr)];
            
            $titleEn = "$topic: $impact ($i)";
            $titleAr = "$topicAr: $impactAr ($i)";
            
            $slug = Str::slug($titleEn);
            
            $contentEn = "Discover how $topic is $impact. We explore best practices, modern tools, and expert tips to help you build outstanding products. In this comprehensive guide, we cover step-by-step procedures to optimize performance and elevate your business. Start implementing these strategies today!";
            $contentAr = "اكتشف كيف يساهم $topicAr في $impactAr. نحن نستكشف أفضل الممارسات، الأدوات الحديثة، ونصائح الخبراء لمساعدتك على بناء منتجات رائعة. في هذا الدليل الشامل، نغطي الإجراءات خطوة بخطوة لتحسين الأداء ورفع مستوى عملك. ابدأ بتطبيق هذه الاستراتيجيات اليوم!";
            
            $randomImageArray = $images[$catKey];
            $image = $randomImageArray[array_rand($randomImageArray)];
            
            $isTrending = ($i % 20 === 0); // 5% trending
            
            $postDate = (clone $createdAtBase)->addDays($i)->addHours(rand(0, 23))->addMinutes(rand(0, 59));

            Post::create([
                'title' => ['en' => $titleEn, 'ar' => $titleAr],
                'slug' => $slug,
                'content' => ['en' => $contentEn, 'ar' => $contentAr],
                'image' => $image,
                'blog_category_id' => $catModel->id,
                'category' => $catKey,
                'is_published' => true,
                'is_trending' => $isTrending,
                'author_id' => $authorId,
                'seo_title' => ['en' => "Learn about $topic", 'ar' => "تعلم المزيد عن $topicAr"],
                'seo_description' => ['en' => "Find out details on $topic and $impact.", 'ar' => "تعرف على التفاصيل المتعلقة بـ $topicAr وكيف يؤثر على $impactAr."],
                'created_at' => $postDate,
                'updated_at' => $postDate,
            ]);
        }
    }
}
