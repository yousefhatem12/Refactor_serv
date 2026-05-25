<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BlogCategory;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

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

        // --- Create Posts ---
        $posts = [
            [
                'title' => ['en' => 'The Future of AI in Modern Web Development', 'ar' => 'مستقبل الذكاء الاصطناعي في تطوير الويب الحديث'],
                'slug' => 'future-ai-modern-web-development',
                'content' => [
                    'en' => 'Artificial intelligence is changing the way we design and write code. From copilots to automated testing, AI is an essential tool for future developers to streamline their workflows and build highly robust web applications.',
                    'ar' => 'يغير الذكاء الاصطناعي طريقة تصميمنا وكتابتنا للأكواد. من المساعدين البرمجيين إلى الاختبار الآلي، يعد الذكاء الاصطناعي أداة أساسية لمطوري المستقبل لتسريع سير العمل وبناء تطبيقات ويب قوية.'
                ],
                'image' => '/our-blog/Link → post-landscape-1.jpg.png',
                'blog_category_id' => $categoryModels['technology']->id,
                'category' => 'technology',
                'is_published' => true,
                'is_trending' => true,
                'author_id' => $authorId,
                'seo_title' => ['en' => 'Future of AI in Web Dev', 'ar' => 'مستقبل الذكاء الاصطناعي في الويب'],
                'seo_description' => ['en' => 'Discover how AI is shaping the future of frontend and backend web development.', 'ar' => 'اكتشف كيف يشكل الذكاء الاصطناعي مستقبل تطوير الويب الأمامي والخلفي.'],
            ],
            [
                'title' => ['en' => '10 Essential UI/UX Design Rules for 2026', 'ar' => 'أهم 10 قواعد لتصميم واجهة المستخدم وتجربة المستخدم لعام 2026'],
                'slug' => 'ui-ux-design-rules-2026',
                'content' => [
                    'en' => 'Good UI/UX is the key to customer retention. In this article, we outline ten essential rules for crafting premium, beautiful, and highly responsive user interfaces that captivate and delight users.',
                    'ar' => 'واجهة المستخدم الجيدة هي المفتاح للاحتفاظ بالعملاء. في هذه المقالة، نستعرض عشرة قواعد أساسية لبناء واجهات مستخدم متميزة وجميلة وسريعة الاستجابة تجذب المستخدم وتسعده.'
                ],
                'image' => '/our-blog/Link → post-landscape-2.jpg.png',
                'blog_category_id' => $categoryModels['design']->id,
                'category' => 'design',
                'is_published' => true,
                'is_trending' => false,
                'author_id' => $authorId,
                'seo_title' => ['en' => '10 UI/UX Design Rules', 'ar' => '10 قواعد لتصميم واجهة المستخدم'],
                'seo_description' => ['en' => 'Learn the most important design principles to elevate your product UX.', 'ar' => 'تعلم أهم مبادئ التصميم لرفع مستوى تجربة المستخدم لمنتجك.'],
            ],
            [
                'title' => ['en' => 'Why Next.js is the Ultimate React Framework', 'ar' => 'لماذا يعتبر إطار عمل Next.js الخيار الأمثل لـ React'],
                'slug' => 'nextjs-ultimate-react-framework',
                'content' => [
                    'en' => 'Next.js provides the best developer experience with all the features you need for production: hybrid static & server rendering, TypeScript support, smart bundling, and route pre-fetching.',
                    'ar' => 'يوفر Next.js أفضل تجربة للمطورين مع جميع الميزات التي تحتاجها للإنتاج: العرض الثابت والمختلط، دعم لغة TypeScript، التجميع الذكي والتحميل المسبق للمسارات.'
                ],
                'image' => '/our-blog/Link → post-landscape-3.jpg.png',
                'blog_category_id' => $categoryModels['development']->id,
                'category' => 'development',
                'is_published' => true,
                'is_trending' => true,
                'author_id' => $authorId,
                'seo_title' => ['en' => 'Next.js: Ultimate React Framework', 'ar' => 'لماذا نستخدم Next.js مع React'],
                'seo_description' => ['en' => 'Find out why developers prefer Next.js for building scalable web apps.', 'ar' => 'تعرف على سبب تفضيل المطورين لـ Next.js لبناء تطبيقات ويب قابلة للتطوير.'],
            ],
            [
                'title' => ['en' => 'SEO Strategies to Rank #1 on Google Search', 'ar' => 'استراتيجيات تحسين محركات البحث لتصدر نتائج بحث جوجل'],
                'slug' => 'seo-strategies-rank-first-google',
                'content' => [
                    'en' => 'SEO is constantly evolving. Learn the top strategies for search engine optimization to boost organic traffic to your site and rank first in search results through structured data, high performance, and keyword optimization.',
                    'ar' => 'سيو يتطور باستمرار. تعلم أهم استراتيجيات تحسين محركات البحث لزيادة عدد الزيارات الطبيعية لموقعك وتصدر نتائج البحث الأولى من خلال البيانات المنظمة والأداء العالي وتحسين الكلمات المفتاحية.'
                ],
                'image' => '/our-blog/Linfgd post-landscape-6.jpg.png',
                'blog_category_id' => $categoryModels['marketing']->id,
                'category' => 'marketing',
                'is_published' => true,
                'is_trending' => false,
                'author_id' => $authorId,
                'seo_title' => ['en' => 'SEO Strategies for Google Rank', 'ar' => 'كيف تتصدر نتائج بحث جوجل'],
                'seo_description' => ['en' => 'Discover latest SEO trends and hacks to rank first on search engines.', 'ar' => 'اكتشف أحدث صيغ وحيل السيو لتصدر نتائج محركات البحث الأولى.'],
            ],
            [
                'title' => ['en' => 'A Guide to Customizing Filament Admin Panels', 'ar' => 'دليل تخصيص لوحات تحكم فيلامينت Filament'],
                'slug' => 'guide-customizing-filament-admin-panels',
                'content' => [
                    'en' => 'Filament is a highly extensible administration panel builder for Laravel. This guide walks you through building custom resource widgets, custom charts, and custom actions to build powerful back offices.',
                    'ar' => 'فيلامينت هو أداة بناء لوحات تحكم قابلة للتوسيع بشكل كبير لـ Laravel. يوضح لك هذا الدليل كيفية إنشاء أدوات الموارد المخصصة والرسوم البيانية والإجراءات المخصصة لبناء خلفية قوية.'
                ],
                'image' => '/our-blog/Link post-landscape-3.jpg.png',
                'blog_category_id' => $categoryModels['development']->id,
                'category' => 'development',
                'is_published' => true,
                'is_trending' => true,
                'author_id' => $authorId,
                'seo_title' => ['en' => 'Customizing Filament Admin Guide', 'ar' => 'دليل تخصيص لوحات تحكم Filament'],
                'seo_description' => ['en' => 'Step-by-step tutorial to customize resources and dashboards in Filament PHP.', 'ar' => 'درس خطوة بخطوة لتخصيص الموارد ولوحات القيادة في Filament PHP.'],
            ],
        ];

        foreach ($posts as $postData) {
            Post::create($postData);
        }
    }
}
