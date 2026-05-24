<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class FullEnglishSeeder extends Seeder
{
    // Helper: puts the same English text in both 'en' and 'ar' keys
    private function t($text) { return ['en' => $text, 'ar' => $text]; }

    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        // ── NAV LINKS ──
        \App\Models\NavLink::truncate();
        $home = \App\Models\NavLink::create(['url' => '/Home', 'label' => $this->t('Home'), 'sort_order' => 1]);
        $aboutParent = \App\Models\NavLink::create(['url' => '#', 'label' => $this->t('About Us'), 'sort_order' => 2]);
        \App\Models\NavLink::create(['url' => '/about-us', 'label' => $this->t('About Us'), 'parent_id' => $aboutParent->id, 'sort_order' => 1]);
        \App\Models\NavLink::create(['url' => '/bank-accounts', 'label' => $this->t('Bank Accounts'), 'parent_id' => $aboutParent->id, 'sort_order' => 2]);
        $services = \App\Models\NavLink::create(['url' => '#', 'label' => $this->t('Services'), 'sort_order' => 3]);
        \App\Models\NavLink::create(['url' => '/portfolio', 'label' => $this->t('Portfolio'), 'sort_order' => 4]);
        \App\Models\NavLink::create(['url' => '/blog', 'label' => $this->t('Blog'), 'sort_order' => 5]);
        \App\Models\NavLink::create(['url' => '/our-products', 'label' => $this->t('Our Products'), 'sort_order' => 6]);
        \App\Models\NavLink::create(['url' => '/careers', 'label' => $this->t('Careers'), 'sort_order' => 7]);
        \App\Models\NavLink::create(['url' => '/faq', 'label' => $this->t('FAQ'), 'sort_order' => 8]);
        \App\Models\NavLink::create(['url' => '/contact-us', 'label' => $this->t('Contact Us'), 'sort_order' => 9]);

        // ── PROJECT CATEGORIES ──
        \App\Models\ProjectCategory::truncate();
        \App\Models\ProjectCategory::create(['slug' => 'websites', 'name' => $this->t('Websites & Web Apps')]);
        \App\Models\ProjectCategory::create(['slug' => 'mobile', 'name' => $this->t('Mobile Applications')]);
        \App\Models\ProjectCategory::create(['slug' => 'digital', 'name' => $this->t('Digital Marketing & Branding')]);

        // ── SERVICES ──
        \App\Models\Service::truncate();
        $servicesList = [
            ['slug' => 'web-development', 'name' => 'Web Development', 'desc' => 'Custom web development solutions using the latest technologies to build fast, scalable, and responsive websites.'],
            ['slug' => 'ui-ux-design', 'name' => 'UI/UX Design', 'desc' => 'Professional user interface and user experience design services that create stunning digital experiences.'],
            ['slug' => 'cloud-hosting', 'name' => 'Cloud Hosting', 'desc' => 'Reliable and fast cloud hosting services with 99.9% uptime guarantee and 24/7 technical support.'],
            ['slug' => 'seo-optimization', 'name' => 'SEO Optimization', 'desc' => 'Search engine optimization services to boost your website rankings and drive organic traffic.'],
            ['slug' => 'cyber-security', 'name' => 'Cyber Security', 'desc' => 'Comprehensive cyber security solutions to protect your business from digital threats and vulnerabilities.'],
            ['slug' => 'social-media', 'name' => 'Social Media Marketing', 'desc' => 'Strategic social media management and marketing to grow your brand presence across all platforms.'],
            ['slug' => 'ecommerce', 'name' => 'E-Commerce Solutions', 'desc' => 'Complete e-commerce platforms with payment integration, inventory management, and beautiful storefronts.'],
            ['slug' => 'mobile-apps', 'name' => 'Mobile App Development', 'desc' => 'Native and cross-platform mobile applications for iOS and Android with modern UI and smooth performance.'],
            ['slug' => 'digital-identity', 'name' => 'Digital Identity', 'desc' => 'Complete brand identity design including logos, brand guidelines, and visual identity systems.'],
            ['slug' => 'network-marketing', 'name' => 'Network Marketing', 'desc' => 'Multi-level marketing solutions and network marketing platforms to scale your business operations.'],
        ];
        foreach ($servicesList as $s) {
            \App\Models\Service::create([
                'slug' => $s['slug'], 'name' => $this->t($s['name']),
                'description' => $this->t($s['desc']), 'is_active' => true,
                'sections' => ['blocks' => [
                    ['type' => 'hero', 'data' => ['title' => $this->t($s['name']), 'subtitle' => $this->t($s['desc'])]],
                    ['type' => 'features', 'data' => ['title' => $this->t('Why Choose Our ' . $s['name'] . ' Service?'), 'items' => [
                        ['title' => $this->t('Expert Team'), 'desc' => $this->t('Our team of certified professionals delivers top-quality results.')],
                        ['title' => $this->t('Fast Delivery'), 'desc' => $this->t('We deliver projects on time without compromising on quality.')],
                        ['title' => $this->t('24/7 Support'), 'desc' => $this->t('Round-the-clock technical support to keep your business running smoothly.')],
                        ['title' => $this->t('Affordable Pricing'), 'desc' => $this->t('Competitive pricing plans designed to fit businesses of all sizes.')],
                    ]]],
                    ['type' => 'how_we_work_timeline', 'data' => ['items' => [
                        ['title' => $this->t('Requirements Analysis'), 'desc' => $this->t("Understanding your business needs and project goals.")],
                        ['title' => $this->t('Planning & Strategy'), 'desc' => $this->t("Creating a detailed roadmap and technical architecture.")],
                        ['title' => $this->t('Design & Development'), 'desc' => $this->t("Building your solution with the latest technologies.")],
                        ['title' => $this->t('Testing & Launch'), 'desc' => $this->t("Thorough testing followed by a smooth deployment.")],
                        ['title' => $this->t('Support & Maintenance'), 'desc' => $this->t("Ongoing support and updates to ensure optimal performance.")],
                    ]]],
                    ['type' => 'blogs', 'data' => []],
                ]],
            ]);
        }

        // ── PAGES ──
        \App\Models\Page::truncate();
        $pages = [
            ['slug' => 'home', 'title' => 'Innovative Software Solutions', 'subtitle' => 'Tailored to your needs. We help you build the future.'],
            ['slug' => 'about-us', 'title' => 'About Us', 'subtitle' => 'Learn more about our journey, our team, and why you should choose us.'],
            ['slug' => 'contact-us', 'title' => 'Contact Us', 'subtitle' => 'Get in touch with us for any inquiries or support.'],
            ['slug' => 'portfolio', 'title' => 'Our Portfolio', 'subtitle' => 'Explore our latest projects and success stories.'],
            ['slug' => 'faq', 'title' => 'Frequently Asked Questions', 'subtitle' => 'Find answers to common questions about our services.'],
            ['slug' => 'blog', 'title' => 'Latest Blog Posts', 'subtitle' => 'Discover articles, tutorials, and industry insights.'],
        ];
        foreach ($pages as $p) {
            \App\Models\Page::create(['slug' => $p['slug'], 'title' => $this->t($p['title']), 'subtitle' => $this->t($p['subtitle']),
                'sections' => ['template' => $p['slug'] === 'about-us' ? 'about' : ($p['slug'] === 'contact-us' ? 'contact' : ($p['slug'] === 'portfolio' ? 'portfolio' : ($p['slug'] === 'faq' ? 'faq' : ($p['slug'] === 'blog' ? 'blog' : null))))]]);
        }

        // ── POSTS ──
        \App\Models\Post::truncate();
        $posts = [
            ['title' => 'Top 10 Web Design Trends for 2026', 'content' => 'Discover the latest web design trends that are shaping the digital landscape in 2026. From AI-powered interfaces to immersive 3D experiences, these trends will help you stay ahead of the competition.', 'category' => 'Design'],
            ['title' => 'How to Choose the Right Hosting Provider', 'content' => 'Choosing the right hosting provider is crucial for your website performance. Learn about the key factors to consider including uptime, speed, security, and scalability options.', 'category' => 'Hosting'],
            ['title' => 'The Ultimate Guide to SEO in 2026', 'content' => 'Master the art of search engine optimization with our comprehensive guide. From keyword research to technical SEO, we cover everything you need to rank higher on Google.', 'category' => 'Marketing'],
            ['title' => 'Building Scalable E-Commerce Platforms', 'content' => 'Learn the best practices for building e-commerce platforms that can handle millions of transactions. We cover architecture, payment systems, and performance optimization.', 'category' => 'Development'],
            ['title' => 'Mobile App Development Best Practices', 'content' => 'Essential tips and best practices for developing high-quality mobile applications. From UI design to performance optimization, make your app stand out.', 'category' => 'Development'],
            ['title' => 'Cyber Security Essentials for Businesses', 'content' => 'Protect your business from cyber threats with these essential security practices. Learn about firewalls, encryption, and employee training strategies.', 'category' => 'Security'],
        ];
        foreach ($posts as $i => $p) {
            \App\Models\Post::create(['title' => $this->t($p['title']), 'slug' => \Illuminate\Support\Str::slug($p['title']),
                'content' => $this->t($p['content']), 'category' => $p['category'],
                'is_published' => true, 'is_trending' => $i < 3, 'author_id' => 1]);
        }

        // ── FAQS ──
        \App\Models\Faq::truncate();
        $faqs = [
            ['q' => 'What services does Serv5 offer?', 'a' => 'We offer web development, mobile app development, UI/UX design, cloud hosting, SEO optimization, social media marketing, e-commerce solutions, cyber security, and digital identity services.'],
            ['q' => 'How long does a typical project take?', 'a' => 'Project timelines vary depending on complexity. A simple website takes 2-4 weeks, while complex web applications may take 2-6 months. We provide detailed timelines during the planning phase.'],
            ['q' => 'Do you provide ongoing support after project delivery?', 'a' => 'Yes, we offer comprehensive post-launch support and maintenance packages. Our team is available 24/7 to handle any technical issues and implement updates.'],
            ['q' => 'What technologies do you use?', 'a' => 'We use cutting-edge technologies including React, Next.js, Laravel, Node.js, Flutter, Swift, Kotlin, AWS, and more. We choose the best tech stack based on your project requirements.'],
            ['q' => 'How much does a project cost?', 'a' => 'Project costs depend on scope, complexity, and timeline. We offer competitive pricing and provide detailed quotes after understanding your requirements. Contact us for a free consultation.'],
            ['q' => 'Can I see examples of your previous work?', 'a' => 'Absolutely! Visit our Portfolio page to browse through our completed projects across various industries including e-commerce, healthcare, education, and more.'],
        ];
        foreach ($faqs as $f) {
            \App\Models\Faq::create(['question' => $this->t($f['q']), 'answer' => $this->t($f['a']), 'is_active' => true]);
        }

        // ── PRODUCTS ──
        \App\Models\Product::truncate();
        $products = [
            ['slug' => 'restaurant-pos', 'name' => 'Restaurant POS System', 'cat' => 'SaaS Products', 'title' => 'Complete Restaurant Management System', 'desc' => 'A powerful point-of-sale system designed specifically for restaurants with order management, kitchen display, and real-time analytics.'],
            ['slug' => 'crm-platform', 'name' => 'CRM Platform', 'cat' => 'SaaS Products', 'title' => 'Customer Relationship Management', 'desc' => 'Manage your customer relationships effectively with our comprehensive CRM platform featuring lead tracking, sales pipeline, and automated workflows.'],
            ['slug' => 'hr-management', 'name' => 'HR Management System', 'cat' => 'Enterprise Solutions', 'title' => 'Human Resources Management', 'desc' => 'Streamline your HR operations with employee management, payroll processing, attendance tracking, and performance evaluation tools.'],
        ];
        foreach ($products as $p) {
            \App\Models\Product::create(['slug' => $p['slug'], 'name' => $this->t($p['name']), 'category' => $this->t($p['cat']),
                'title' => $this->t($p['title']), 'description' => $this->t($p['desc']), 'is_active' => true,
                'demo_link' => '#', 'order_link' => '/contact-us', 'tech_specs' => ['Laravel', 'React', 'MySQL', 'Redis'],
                'features' => ['en' => [
                    ['title' => 'Dashboard Analytics', 'desc' => 'Real-time dashboards with comprehensive business insights.'],
                    ['title' => 'Multi-language Support', 'desc' => 'Full Arabic and English interface support.'],
                    ['title' => 'Cloud-based', 'desc' => 'Access your data from anywhere with secure cloud hosting.'],
                    ['title' => 'API Integration', 'desc' => 'Seamless integration with third-party services and payment gateways.'],
                ], 'ar' => [
                    ['title' => 'Dashboard Analytics', 'desc' => 'Real-time dashboards with comprehensive business insights.'],
                    ['title' => 'Multi-language Support', 'desc' => 'Full Arabic and English interface support.'],
                    ['title' => 'Cloud-based', 'desc' => 'Access your data from anywhere with secure cloud hosting.'],
                    ['title' => 'API Integration', 'desc' => 'Seamless integration with third-party services and payment gateways.'],
                ]]]);
        }

        // ── CAREERS ──
        \App\Models\Career::truncate();
        $careers = [
            ['title' => 'Senior Full Stack Developer', 'dept' => 'Engineering', 'loc' => 'Cairo, Egypt', 'sal' => '$2,000 - $4,000', 'desc' => 'We are looking for an experienced Full Stack Developer to join our engineering team and build scalable web applications.'],
            ['title' => 'UI/UX Designer', 'dept' => 'Design', 'loc' => 'Remote', 'sal' => '$1,500 - $3,000', 'desc' => 'Join our design team to create beautiful and intuitive user interfaces for web and mobile applications.'],
            ['title' => 'Digital Marketing Specialist', 'dept' => 'Marketing', 'loc' => 'Riyadh, Saudi Arabia', 'sal' => '$1,800 - $3,500', 'desc' => 'Drive our digital marketing efforts across multiple channels including SEO, social media, and paid advertising.'],
        ];
        foreach ($careers as $c) {
            \App\Models\Career::create(['title' => $this->t($c['title']), 'department' => $c['dept'],
                'location' => $this->t($c['loc']), 'salary' => $this->t($c['sal']),
                'description' => $this->t($c['desc']), 'is_active' => true,
                'responsibilities' => ['en' => [['responsibility' => 'Develop and maintain high-quality applications'], ['responsibility' => 'Collaborate with cross-functional teams'], ['responsibility' => 'Write clean, maintainable code'], ['responsibility' => 'Participate in code reviews and technical discussions']],
                    'ar' => [['responsibility' => 'Develop and maintain high-quality applications'], ['responsibility' => 'Collaborate with cross-functional teams'], ['responsibility' => 'Write clean, maintainable code'], ['responsibility' => 'Participate in code reviews and technical discussions']]],
                'requirements' => ['en' => [['requirement' => '3+ years of professional experience'], ['requirement' => 'Strong problem-solving skills'], ['requirement' => 'Excellent communication skills'], ['requirement' => 'Bachelor degree in Computer Science or related field']],
                    'ar' => [['requirement' => '3+ years of professional experience'], ['requirement' => 'Strong problem-solving skills'], ['requirement' => 'Excellent communication skills'], ['requirement' => 'Bachelor degree in Computer Science or related field']]]]);
        }

        // ── BANK ACCOUNTS ──
        \App\Models\BankAccount::truncate();
        \App\Models\BankAccount::create(['name' => $this->t('National Bank of Egypt'), 'owner' => $this->t('Serv5 Technology Co.'), 'account' => $this->t('0012345678901'), 'country' => 'egypt', 'type' => 'bank', 'brand_color' => 'from-[#1a472a] to-[#2d7a4a]', 'shadow' => 'rgba(26,71,42,0.4)', 'sort_order' => 1]);
        \App\Models\BankAccount::create(['name' => $this->t('Al Rajhi Bank'), 'owner' => $this->t('Serv5 Technology Co.'), 'account' => $this->t('SA0380000000608010167519'), 'country' => 'ksa', 'type' => 'bank', 'brand_color' => 'from-[#003366] to-[#005599]', 'shadow' => 'rgba(0,51,102,0.4)', 'sort_order' => 2]);
        \App\Models\BankAccount::create(['name' => $this->t('Vodafone Cash'), 'owner' => $this->t('Serv5 Technology Co.'), 'account' => $this->t('01012345678'), 'country' => 'egypt', 'type' => 'wallet', 'brand_color' => 'from-[#e60000] to-[#ff3333]', 'shadow' => 'rgba(230,0,0,0.4)', 'sort_order' => 3]);

        // ── PROJECTS ──
        \App\Models\Project::truncate();
        $projects = [
            ['name' => 'E-Commerce Platform', 'cat' => 'websites', 'desc' => 'A fully featured modern e-commerce platform with payment integration.', 'client' => 'Tech Corp', 'tags' => ['Laravel', 'React', 'Stripe']],
            ['name' => 'Healthcare Mobile App', 'cat' => 'mobile', 'desc' => 'A comprehensive healthcare app for appointment booking and telemedicine.', 'client' => 'MedCare Inc.', 'tags' => ['Flutter', 'Firebase', 'Node.js']],
            ['name' => 'Brand Identity Design', 'cat' => 'digital', 'desc' => 'Complete brand identity including logo, guidelines, and marketing materials.', 'client' => 'StartUp Hub', 'tags' => ['Branding', 'Illustrator', 'Figma']],
            ['name' => 'Real Estate Portal', 'cat' => 'websites', 'desc' => 'Property listing and management portal with virtual tours and map integration.', 'client' => 'Property Plus', 'tags' => ['Next.js', 'PostgreSQL', 'Google Maps']],
            ['name' => 'Food Delivery App', 'cat' => 'mobile', 'desc' => 'On-demand food delivery application with real-time tracking and driver management.', 'client' => 'QuickBite', 'tags' => ['React Native', 'Node.js', 'MongoDB']],
            ['name' => 'Social Media Campaign', 'cat' => 'digital', 'desc' => 'Multi-platform social media marketing campaign with content creation and analytics.', 'client' => 'Fashion Brand', 'tags' => ['Marketing', 'Content', 'Analytics']],
        ];
        foreach ($projects as $p) {
            \App\Models\Project::create(['name' => $this->t($p['name']), 'category' => $p['cat'],
                'description' => $this->t($p['desc']), 'client' => $p['client'],
                'url' => 'https://example.com', 'tags' => $p['tags'], 'type' => 'portfolio']);
        }

        // ── FOOTER LINKS ──
        \App\Models\FooterLink::truncate();
        $footer = [
            'useful_links' => ['Landing Page', 'Popup Builder', 'Web Design', 'Content', 'Integrations'],
            'quick_links' => ['Web Designers', 'Marketers', 'Small Business', 'Website Builder'],
            'offers' => ['Academy', 'Blog', 'Themes', 'Hosting', 'Developers', 'Support'],
            'company' => ['About Us', 'Careers', 'FAQ', 'Teams', 'Contact Us'],
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
        \App\Models\Setting::updateOrCreate(['key' => 'site_address'], ['value' => json_encode(['en' => 'Egypt - Mansoura - Suez Canal St. above Dr. Ramy Pharmacy', 'ar' => 'مصر - المنصورة - شارع قناة السويس - أعلى صيدلية د. رامي']), 'type' => 'text']);
        \App\Models\Setting::updateOrCreate(['key' => 'footer_heading'], ['value' => json_encode($this->t('Have a Great Web Design<br/>With Serv5.')), 'type' => 'text']);
        \App\Models\Setting::updateOrCreate(['key' => 'footer_description'], ['value' => json_encode($this->t('Serv5 specializes in modern web solutions, delivering high-quality digital products.')), 'type' => 'text']);
        \App\Models\Setting::updateOrCreate(['key' => 'footer_button_text'], ['value' => json_encode($this->t('Get Started')), 'type' => 'text']);
        \App\Models\Setting::updateOrCreate(['key' => 'footer_copyright'], ['value' => json_encode($this->t('2026 © Serv5. All Rights Reserved.')), 'type' => 'text']);

        // ── SOCIAL LINKS ──
        \App\Models\SocialLink::truncate();
        \App\Models\SocialLink::create(['platform' => 'facebook', 'url' => 'https://facebook.com/serv5', 'is_active' => true]);
        \App\Models\SocialLink::create(['platform' => 'twitter', 'url' => 'https://x.com/serv5', 'is_active' => true]);
        \App\Models\SocialLink::create(['platform' => 'instagram', 'url' => 'https://instagram.com/serv5', 'is_active' => true]);
        \App\Models\SocialLink::create(['platform' => 'linkedin', 'url' => 'https://linkedin.com/company/serv5', 'is_active' => true]);

        // ── TESTIMONIALS ──
        \App\Models\Testimonial::truncate();
        \App\Models\Testimonial::create([
            'client_name' => ['en' => 'James Wilson', 'ar' => 'جيمس ويلسون'],
            'position' => ['en' => 'CEO, Tech Solutions', 'ar' => 'الرئيس التنفيذي لشركة تيك سوليوشنز'],
            'review' => [
                'en' => 'Serv5 delivered an exceptional e-commerce platform that transformed our business. Their team was professional, responsive, and went above expectations.',
                'ar' => 'قدمت لنا Serv5 منصة تجارة إلكترونية استثنائية غيرت مجرى أعمالنا تماماً. كان فريقهم محترفاً للغاية ومتعاوناً وتجاوز توقعاتنا بكثير.'
            ],
            'is_active' => true
        ]);
        \App\Models\Testimonial::create([
            'client_name' => ['en' => 'Sarah Johnson', 'ar' => 'سارة جونسون'],
            'position' => ['en' => 'Marketing Director, BrandCo', 'ar' => 'مديرة التسويق في براند كو'],
            'review' => [
                'en' => 'The social media strategy developed by Serv5 increased our engagement by 300%. Their creative approach and data-driven decisions are outstanding.',
                'ar' => 'الاستراتيجية الإعلانية المتميزة والتسويق عبر وسائل التواصل التي صممتها Serv5 رفعت نسبة التفاعل لدينا بمعدل 300%. نتائج مذهلة فعلاً.'
            ],
            'is_active' => true
        ]);
        \App\Models\Testimonial::create([
            'client_name' => ['en' => 'Michael Brown', 'ar' => 'مايكل براون'],
            'position' => ['en' => 'CTO, FinanceApp', 'ar' => 'المدير التقني لتطبيق فاينانس آب'],
            'review' => [
                'en' => 'Working with Serv5 on our mobile app was a great experience. They delivered a high-quality product on time and within budget. Highly recommended!',
                'ar' => 'كان العمل مع شركة Serv5 لتطوير تطبيق الهاتف الخاص بنا تجربة رائعة ومريحة. سلمونا تطبيقاً متكاملاً بجودة ممتازة في الوقت المحدد تماماً.'
            ],
            'is_active' => true
        ]);

        Schema::enableForeignKeyConstraints();
        $this->command->info('Full English content seeded successfully!');
    }
}
