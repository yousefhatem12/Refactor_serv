<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InitialContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Navigation Links
        $home = \App\Models\NavLink::updateOrCreate(['url' => '/Home'], ['label' => ['en' => 'Home', 'ar' => 'الرئيسية'], 'sort_order' => 1]);
        $about = \App\Models\NavLink::updateOrCreate(['url' => '/about-us'], ['label' => ['en' => 'About us', 'ar' => 'من نحن'], 'sort_order' => 2]);
        $services = \App\Models\NavLink::updateOrCreate(['url' => '#'], ['label' => ['en' => 'Services', 'ar' => 'خدماتنا'], 'sort_order' => 3]);
        
        \App\Models\NavLink::updateOrCreate(['url' => '/services-web-dev'], ['label' => ['en' => 'Web Development', 'ar' => 'تطوير المواقع'], 'parent_id' => $services->id, 'sort_order' => 1]);
        \App\Models\NavLink::updateOrCreate(['url' => '/services-web-design'], ['label' => ['en' => 'Web Design', 'ar' => 'تصميم المواقع'], 'parent_id' => $services->id, 'sort_order' => 2]);
        \App\Models\NavLink::updateOrCreate(['url' => '/services-social-media'], ['label' => ['en' => 'Social Media', 'ar' => 'سوشيال ميديا'], 'parent_id' => $services->id, 'sort_order' => 3]);
        \App\Models\NavLink::updateOrCreate(['url' => '/services-web-hosting'], ['label' => ['en' => 'Web Hosting', 'ar' => 'استضافة المواقع'], 'parent_id' => $services->id, 'sort_order' => 4]);

        \App\Models\NavLink::updateOrCreate(['url' => '/portfolio'], ['label' => ['en' => 'Portfolio', 'ar' => 'أعمالنا'], 'sort_order' => 4]);
        \App\Models\NavLink::updateOrCreate(['url' => '/blog'], ['label' => ['en' => 'Blog', 'ar' => 'المدونة'], 'sort_order' => 5]);
        \App\Models\NavLink::updateOrCreate(['url' => '/faq'], ['label' => ['en' => 'FAQ', 'ar' => 'الأسئلة الشائعة'], 'sort_order' => 6]);
        \App\Models\NavLink::updateOrCreate(['url' => '/contact-us'], ['label' => ['en' => 'Contact Us', 'ar' => 'اتصل بنا'], 'sort_order' => 7]);

        // Pages
        $pages = [
            [
                'slug' => 'about-us',
                'title' => ['en' => 'About Us', 'ar' => 'من نحن'],
                'subtitle' => ['en' => 'Learn more about our journey, our team, and why you should choose us.', 'ar' => 'تعرف أكثر على رحلتنا، فريقنا، ولماذا يجب عليك اختيارنا.'],
                'sections' => [
                    'en' => [
                        [
                            'type' => 'text_image',
                            'data' => [
                                'title' => 'Our Mission',
                                'tagline' => 'Innovation at heart',
                                'content' => 'We aim to provide the best software solutions for businesses worldwide.',
                                'layout' => 'text_left',
                            ]
                        ]
                    ],
                    'ar' => [
                        [
                            'type' => 'text_image',
                            'data' => [
                                'title' => 'مهمتنا',
                                'tagline' => 'الابتكار في القلب',
                                'content' => 'نهدف إلى تقديم أفضل الحلول البرمجية للشركات في جميع أنحاء العالم.',
                                'layout' => 'text_left',
                            ]
                        ]
                    ]
                ]
            ],
            [
                'slug' => 'contact-us',
                'title' => ['en' => 'Contact Us', 'ar' => 'اتصل بنا'],
                'subtitle' => ['en' => 'Get in touch with us for any inquiries or support.', 'ar' => 'تواصل معنا لأي استفسارات أو دعم.'],
            ],
            [
                'slug' => 'portfolio',
                'title' => ['en' => 'Our Portfolio', 'ar' => 'أعمالنا'],
                'subtitle' => ['en' => 'Explore our latest projects and success stories.', 'ar' => 'استكشف أحدث مشاريعنا وقصص نجاحنا.'],
            ],
            [
                'slug' => 'faq',
                'title' => ['en' => 'Frequently Asked Questions', 'ar' => 'الأسئلة الشائعة'],
                'subtitle' => ['en' => 'Find answers to common questions about our services.', 'ar' => 'جد أجوبة للأسئلة الشائعة حول خدماتنا.'],
            ],
            [
                'slug' => 'home',
                'title' => ['en' => 'Innovative Solutions', 'ar' => 'حلول مبتكرة'],
                'subtitle' => ['en' => 'Tailored to your needs. We help you build the future.', 'ar' => 'مصممة خصيصاً لاحتياجاتك. نحن نساعدك على بناء المستقبل.'],
            ],
            [
                'slug' => 'blog',
                'title' => ['en' => 'Recent Blog Posts', 'ar' => 'أحدث المقالات'],
                'subtitle' => ['en' => 'Discover articles and tutorials to help you build better.', 'ar' => 'اكتشف المقالات والدروس التعليمية لمساعدتك على البناء بشكل أفضل.'],
            ],
            [
                'slug' => 'services-web-dev',
                'title' => ['en' => 'Web Development', 'ar' => 'تطوير المواقع'],
                'subtitle' => ['en' => 'Get the best web development services for your business.', 'ar' => 'احصل على أفضل خدمات تطوير المواقع لعملك.'],
            ],
        ];

        foreach ($pages as $page) {
            \App\Models\Page::updateOrCreate(['slug' => $page['slug']], $page);
        }
    }
}
