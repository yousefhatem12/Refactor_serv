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
        // Clear existing navigation links to avoid duplicates
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        \App\Models\NavLink::truncate();
        \App\Models\ProjectCategory::truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        // Project Categories
        \App\Models\ProjectCategory::create(['slug' => 'websites', 'name' => ['en' => 'Websites & Web Apps', 'ar' => 'مواقع إلكترونية']]);
        \App\Models\ProjectCategory::create(['slug' => 'mobile', 'name' => ['en' => 'Mobile Applications', 'ar' => 'تطبيقات جوال']]);
        \App\Models\ProjectCategory::create(['slug' => 'motion', 'name' => ['en' => 'Motion Graphics', 'ar' => 'موشن جرافيك']]);
        \App\Models\ProjectCategory::create(['slug' => 'digital', 'name' => ['en' => 'Digital Identity & Branding', 'ar' => 'هوية رقمية']]);
        \App\Models\ProjectCategory::create(['slug' => 'social', 'name' => ['en' => 'Social Media Designs', 'ar' => 'سوشيال ميديا']]);

        // Navigation Links
        $home = \App\Models\NavLink::create(['url' => '/Home', 'label' => ['en' => 'Home', 'ar' => 'الرئيسية'], 'sort_order' => 1]);
        $about = \App\Models\NavLink::create(['url' => '/about-us', 'label' => ['en' => 'About us', 'ar' => 'من نحن'], 'sort_order' => 2]);
        $services = \App\Models\NavLink::create(['url' => '#', 'label' => ['en' => 'Services', 'ar' => 'خدماتنا'], 'sort_order' => 3]);
        
        \App\Models\NavLink::create(['url' => '/services-web-dev', 'label' => ['en' => 'Web Development', 'ar' => 'تطوير المواقع'], 'parent_id' => $services->id, 'sort_order' => 1]);
        \App\Models\NavLink::create(['url' => '/services-web-design', 'label' => ['en' => 'Web Design', 'ar' => 'تصميم المواقع'], 'parent_id' => $services->id, 'sort_order' => 2]);
        \App\Models\NavLink::create(['url' => '/services-social-media', 'label' => ['en' => 'Social Media', 'ar' => 'سوشيال ميديا'], 'parent_id' => $services->id, 'sort_order' => 3]);
        \App\Models\NavLink::create(['url' => '/services-web-hosting', 'label' => ['en' => 'Web Hosting', 'ar' => 'استضافة المواقع'], 'parent_id' => $services->id, 'sort_order' => 4]);

        \App\Models\NavLink::create(['url' => '/portfolio', 'label' => ['en' => 'Our Works', 'ar' => 'أعمالنا'], 'sort_order' => 4]);
        \App\Models\NavLink::create(['url' => '/blog', 'label' => ['en' => 'Blog', 'ar' => 'المدونة'], 'sort_order' => 5]);
        \App\Models\NavLink::create(['url' => '/our-products', 'label' => ['en' => 'Our Products', 'ar' => 'منتجاتنا'], 'sort_order' => 6]);
        \App\Models\NavLink::create(['url' => '/careers', 'label' => ['en' => 'Careers', 'ar' => 'الوظائف'], 'sort_order' => 7]);
        \App\Models\NavLink::create(['url' => '/faq', 'label' => ['en' => 'FAQ', 'ar' => 'الأسئلة الشائعة'], 'sort_order' => 8]);
        \App\Models\NavLink::create(['url' => '/contact-us', 'label' => ['en' => 'Contact Us', 'ar' => 'اتصل بنا'], 'sort_order' => 9]);

        // Pages
        $pages = [
            [
                'slug' => 'about-us',
                'title' => ['en' => 'About Us', 'ar' => 'من نحن'],
                'subtitle' => ['en' => 'Learn more about our journey, our team, and why you should choose us.', 'ar' => 'تعرف أكثر على رحلتنا، فريقنا، ولماذا يجب عليك اختيارنا.'],
                'sections' => [
                    'en' => [
                        [
                            'type' => 'stats',
                            'data' => [
                                'items' => [
                                    ['label' => 'We bring over 12 years of industry expertise to deliver top-tier software solutions tailored for your business needs.', 'value' => '12+ Years Of Experience'],
                                    ['label' => 'Our commitment to excellence ensures that our clients consistently receive the best service and support.', 'value' => '99% Client Satisfaction'],
                                    ['label' => 'A robust portfolio of completed projects across various industries, showcasing our capability and reliability.', 'value' => '100+ Successful Projects'],
                                ]
                            ]
                        ],
                        [
                            'type' => 'text_image',
                            'data' => [
                                'title' => '15 Years of Experience in Software Development',
                                'tagline' => 'Experience',
                                'content' => "Serv5 provides a range of cutting-edge, customized software solutions for global enterprise clients.\n\nOur mission is to enable businesses to thrive in the digital age by delivering innovative software solutions.",
                                'layout' => 'text_left',
                            ]
                        ],
                        [
                            'type' => 'text_image',
                            'data' => [
                                'title' => 'Innovative Solutions Tailored to Your Needs',
                                'tagline' => 'Solutions',
                                'content' => "We partner with businesses to identify their specific challenges and craft innovative solutions tailored to their exact requirements.\n\nBy leveraging the latest technologies and agile development methodologies, we ensure that our solutions not only meet your current needs but also adapt seamlessly to your future business goals.",
                                'layout' => 'text_right',
                            ]
                        ],
                        [
                            'type' => 'journey',
                            'data' => [
                                'title' => 'OUR JOURNEY',
                                'description' => 'A decade of growth, innovation, and partnership.',
                                'items' => [
                                    ['year' => '2009', 'location' => 'Egypt', 'users' => '10 Active Clients', 'partnership' => '', 'description' => 'Started our operations in Egypt with a small, dedicated team of developers.'],
                                    ['year' => '2011', 'location' => 'Egypt', 'users' => '20 Active Clients', 'partnership' => '', 'description' => 'Expanded our team and client base, delivering custom software solutions.'],
                                    ['year' => '2013', 'location' => 'Egypt & Saudi Arabia', 'users' => '35 Active Clients', 'partnership' => '', 'description' => 'Entered the Gulf market, establishing a presence in Saudi Arabia.'],
                                    ['year' => '2015', 'location' => 'Egypt & Saudi Arabia', 'users' => '40 Active Clients', 'partnership' => 'Etihad Airways Partnership', 'description' => 'Formed strategic partnerships with major companies in the region.'],
                                    ['year' => '2017', 'location' => 'Egypt & Saudi Arabia', 'users' => '35 Active Clients', 'partnership' => 'Etihad Airways Partnership', 'description' => 'Launched our specialized SaaS products for enterprise management.'],
                                    ['year' => '2020', 'location' => 'Egypt & Saudi Arabia', 'users' => '35 Active Clients', 'partnership' => 'Etihad Airways Partnership', 'description' => 'Navigated digital transformation trends and supported remote work infrastructure.'],
                                    ['year' => '2024', 'location' => 'Egypt & Saudi Arabia', 'users' => '35 Active Clients', 'partnership' => 'Etihad Airways Partnership', 'description' => 'Continuing our legacy of delivering high-quality custom solutions worldwide.'],
                                ]
                            ]
                        ],
                        [
                            'type' => 'global_reach',
                            'data' => [
                                'title' => 'WE ARE HERE FOR YOU',
                                'description' => 'We serve clients across the globe from our strategic regional offices.',
                                'items' => [
                                    ['city' => 'Mansoura', 'name' => 'Egypt', 'details' => 'Suez canal above Dr.Rami pharmacy', 'x' => 59, 'y' => 42],
                                    ['city' => 'Riyadh', 'name' => 'Saudi Arabia', 'details' => 'Headquarters', 'x' => 63, 'y' => 50],
                                    ['city' => 'Dubai', 'name' => 'UAE', 'details' => 'Innovation Hub', 'x' => 66, 'y' => 51],
                                    ['city' => 'Mumbai', 'name' => 'India', 'details' => 'Tech Center', 'x' => 76, 'y' => 58],
                                    ['city' => 'Sydney', 'name' => 'Australia', 'details' => 'Growth Center', 'x' => 89, 'y' => 81],
                                    ['city' => 'New York', 'name' => 'USA', 'details' => 'Business Office', 'x' => 18, 'y' => 38],
                                    ['city' => 'São Paulo', 'name' => 'Brazil', 'details' => 'Regional Center', 'x' => 31, 'y' => 74],
                                    ['city' => 'Cape Town', 'name' => 'South Africa', 'details' => 'Operations Hub', 'x' => 55, 'y' => 82],
                                ]
                            ]
                        ],
                        [
                            'type' => 'features',
                            'data' => [
                                'title' => 'Why Choose Us?',
                                'items' => [
                                    ['title' => 'Trusted Partner', 'description' => 'We are a trusted partner in driving digital transformation.', 'icon' => 'FaHandshake'],
                                    ['title' => 'Innovative Solutions', 'description' => 'We deliver innovative, tailored solutions that drive business growth.', 'icon' => 'FaLightbulb'],
                                    ['title' => 'Expert Team', 'description' => 'Our team consists of highly skilled experts dedicated to excellence.', 'icon' => 'FaUsers'],
                                    ['title' => 'Client-Centric', 'description' => 'We prioritize our clients\' needs, ensuring that every project is aligned.', 'icon' => 'FaHeart'],
                                    ['title' => 'Proven Methodology', 'description' => 'We employ a proven methodology for success, combining agile practices.', 'icon' => 'FaChartLine'],
                                    ['title' => 'Continuous Support', 'description' => 'We provide ongoing support and maintenance to ensure optimal performance.', 'icon' => 'FaHeadset'],
                                ]
                            ]
                        ],
                        [
                            'type' => 'team',
                            'data' => [
                                'title' => 'MEET FOUNDERS',
                                'description' => 'We are a team of passionate professionals dedicated to bringing your ideas to life.',
                                'items' => [
                                    ['name' => 'John Doe', 'role' => 'Chief Creative Officer', 'image' => ''],
                                    ['name' => 'Jane Smith', 'role' => 'Chief Technology Officer', 'image' => ''],
                                    ['name' => 'Ahmed Ali', 'role' => 'Chief Marketing Officer', 'image' => ''],
                                ]
                            ]
                        ],
                        [
                            'type' => 'cta',
                            'data' => [
                                'title' => 'Ready to take your business to the next level?',
                                'description' => 'Contact us today to explore how our innovative software solutions can drive growth, streamline operations, and unlock your full potential in the digital era.',
                                'button_text' => 'Contact Us',
                                'button_url' => '/contact-us',
                            ]
                        ],
                        [
                            'type' => 'team_work',
                            'data' => [
                                'title' => 'COLLABORATION, CRAFTSMANSHIP, & CREATIVE IMPACT',
                                'description' => 'Fostering a creative environment where diverse ideas cross-pollinate to engineer cutting-edge digital products.',
                                'pillars' => [
                                    [
                                        'title' => 'Synergy & Innovation',
                                        'description' => 'Fostering a creative environment where diverse ideas cross-pollinate to engineer cutting-edge digital products.',
                                        'icon' => 'FaUsers'
                                    ],
                                    [
                                        'title' => 'Technical Excellence',
                                        'description' => 'Committed to state-of-the-art software craftsmanship, solid system design, and clean, future-proof architectures.',
                                        'icon' => 'FaCode'
                                    ],
                                    [
                                        'title' => 'Impact-Driven Design',
                                        'description' => 'Blending visual mastery with user psychology to forge beautiful, seamless, and high-converting customer journeys.',
                                        'icon' => 'FaPaintBrush'
                                    ]
                                ],
                                'members' => [
                                    [
                                        'name' => 'MOHAMED ABDELAL',
                                        'role' => 'Chief Creative Officer & Founder',
                                        'dept' => 'management',
                                        'image' => 'pages/person-1.webp',
                                        'linkedin' => 'https://linkedin.com',
                                        'github' => '#',
                                        'twitter' => 'https://x.com'
                                    ],
                                    [
                                        'name' => 'AHMED MOHAMED',
                                        'role' => 'Chief Technology Officer & Founder',
                                        'dept' => 'management',
                                        'image' => 'pages/person-2.webp',
                                        'linkedin' => 'https://linkedin.com',
                                        'github' => 'https://github.com',
                                        'twitter' => 'https://x.com'
                                    ],
                                    [
                                        'name' => 'AHMED RAMADAN',
                                        'role' => 'Chief Marketing Officer & Founder',
                                        'dept' => 'management',
                                        'image' => 'pages/person-3.webp',
                                        'linkedin' => 'https://linkedin.com',
                                        'github' => '#',
                                        'twitter' => 'https://x.com'
                                    ],
                                    [
                                        'name' => 'Sarah Jenkins',
                                        'role' => 'Lead UI/UX Designer',
                                        'dept' => 'design',
                                        'image' => 'pages/person-1.webp',
                                        'linkedin' => 'https://linkedin.com',
                                        'github' => '#',
                                        'twitter' => 'https://x.com'
                                    ],
                                    [
                                        'name' => 'David Chen',
                                        'role' => 'Senior Full Stack Developer',
                                        'dept' => 'development',
                                        'image' => 'pages/person-2.webp',
                                        'linkedin' => 'https://linkedin.com',
                                        'github' => 'https://github.com',
                                        'twitter' => 'https://x.com'
                                    ],
                                    [
                                        'name' => 'Amira Youssef',
                                        'role' => 'Senior Frontend Engineer',
                                        'dept' => 'development',
                                        'image' => 'pages/person-3.webp',
                                        'linkedin' => 'https://linkedin.com',
                                        'github' => 'https://github.com',
                                        'twitter' => 'https://x.com'
                                    ],
                                    [
                                        'name' => 'Kareem Mahmoud',
                                        'role' => 'SEO & Growth Marketing Expert',
                                        'dept' => 'marketing',
                                        'image' => 'pages/person-2.webp',
                                        'linkedin' => 'https://linkedin.com',
                                        'github' => '#',
                                        'twitter' => 'https://x.com'
                                    ],
                                    [
                                        'name' => 'Emily Taylor',
                                        'role' => 'Art Director',
                                        'dept' => 'design',
                                        'image' => 'pages/person-1.webp',
                                        'linkedin' => 'https://linkedin.com',
                                        'github' => '#',
                                        'twitter' => 'https://x.com'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'ar' => [
                        [
                            'type' => 'stats',
                            'data' => [
                                'items' => [
                                    ['label' => 'نمتلك أكثر من 12 عاماً من الخبرة في الصناعة لتقديم حلول برمجية من الدرجة الأولى مصممة خصيصاً لتلبية احتياجات عملك.', 'value' => '+12 عاماً من الخبرة'],
                                    ['label' => 'التزامنا بالتميز يضمن لعملائنا الحصول باستمرار على أفضل خدمة ودعم ممكنين.', 'value' => '99% رضا العملاء'],
                                    ['label' => 'ملف أعمال قوي من المشاريع المنجزة في مختلف القطاعات، مما يعكس قدراتنا وموثوقيتنا العالية.', 'value' => '+100 مشروع ناجح'],
                                ]
                            ]
                        ],
                        [
                            'type' => 'text_image',
                            'data' => [
                                'title' => '15 عاماً من الخبرة في تطوير البرمجيات',
                                'tagline' => 'الخبرة',
                                'content' => "توفر Serv5 مجموعة من الحلول البرمجية المتطورة والمخصصة لعملاء المؤسسات العالمية.\n\nمهمتنا هي تمكين الشركات من الازدهار في العصر الرقمي من خلال تقديم حلول برمجية مبتكرة.",
                                'layout' => 'text_left',
                            ]
                        ],
                        [
                            'type' => 'text_image',
                            'data' => [
                                'title' => 'حلول مبتكرة مصممة لتلبية احتياجاتك',
                                'tagline' => 'الحلول',
                                'content' => "نحن نتشارك مع الشركات لتحديد التحديات الخاصة بهم وصياغة حلول مبتكرة مصممة وفقاً لمتطلباتهم الدقيقة.\n\nمن خلال الاستفادة من أحدث التقنيات ومنهجيات التطوير الرشيقة (Agile)، نضمن أن حلولنا لا تلبي احتياجاتك الحالية فحسب بل تتكيف أيضاً بسلاسة مع أهداف عملك المستقبلية.",
                                'layout' => 'text_right',
                            ]
                        ],
                        [
                            'type' => 'journey',
                            'data' => [
                                'title' => 'رحلتنا',
                                'description' => 'عقد من النمو، والابتكار، والشراكة.',
                                'items' => [
                                    ['year' => '2009', 'location' => 'مصر', 'users' => '10 عملاء نشطين', 'partnership' => '', 'description' => 'بدأنا عملياتنا في مصر بفريق صغير ومتخصص من المطورين.'],
                                    ['year' => '2011', 'location' => 'مصر', 'users' => '20 عميلاً نشطاً', 'partnership' => '', 'description' => 'قمنا بتوسيع فريقنا وقاعدة عملائنا، وتقديم حلول برمجية مخصصة.'],
                                    ['year' => '2013', 'location' => 'مصر والسعودية', 'users' => '35 عميلاً نشطاً', 'partnership' => '', 'description' => 'دخلنا السوق الخليجي، وأسسنا حضوراً قوياً في المملكة العربية السعودية.'],
                                    ['year' => '2015', 'location' => 'مصر والسعودية', 'users' => '40 عميلاً نشطاً', 'partnership' => 'شراكة طيران الاتحاد', 'description' => 'قمنا ببناء شراكات استراتيجية مع كبرى الشركات في المنطقة.'],
                                    ['year' => '2017', 'location' => 'مصر والسعودية', 'users' => '35 عميلاً نشطاً', 'partnership' => 'شراكة طيران الاتحاد', 'description' => 'أطلقنا منتجات SaaS المتخصصة لإدارة المؤسسات والشركات.'],
                                    ['year' => '2020', 'location' => 'مصر والسعودية', 'users' => '35 عميلاً نشطاً', 'partnership' => 'شراكة طيران الاتحاد', 'description' => 'واكبنا اتجاهات التحول الرقمي ودعمنا البنية التحتية للعمل عن بعد.'],
                                    ['year' => '2024', 'location' => 'مصر والسعودية', 'users' => '35 عميلاً نشطاً', 'partnership' => 'شراكة طيران الاتحاد', 'description' => 'نواصل مسيرتنا في تقديم حلول مخصصة عالية الجودة حول العالم.'],
                                ]
                            ]
                        ],
                        [
                            'type' => 'global_reach',
                            'data' => [
                                'title' => 'نحن هنا من أجلك',
                                'description' => 'نخدم عملائنا في جميع أنحاء العالم من خلال مكاتبنا الإقليمية الاستراتيجية.',
                                'items' => [
                                    ['city' => 'المنصورة', 'name' => 'مصر', 'details' => 'شارع قناة السويس أعلى صيدلية د. رامي', 'x' => 59, 'y' => 42],
                                    ['city' => 'الرياض', 'name' => 'المملكة العربية السعودية', 'details' => 'المقر الرئيسي', 'x' => 63, 'y' => 50],
                                    ['city' => 'دبي', 'name' => 'الإمارات', 'details' => 'مركز الابتكار', 'x' => 66, 'y' => 51],
                                    ['city' => 'مومباي', 'name' => 'الهند', 'details' => 'المركز التقني', 'x' => 76, 'y' => 58],
                                    ['city' => 'سيدني', 'name' => 'أستراليا', 'details' => 'مركز النمو', 'x' => 89, 'y' => 81],
                                    ['city' => 'نيويورك', 'name' => 'الولايات المتحدة', 'details' => 'المكتب التجاري', 'x' => 18, 'y' => 38],
                                    ['city' => 'ساو باولو', 'name' => 'البرازيل', 'details' => 'المركز الإقليمي', 'x' => 31, 'y' => 74],
                                    ['city' => 'كيب تاون', 'name' => 'جنوب أفريقيا', 'details' => 'مركز العمليات', 'x' => 55, 'y' => 82],
                                ]
                            ]
                        ],
                        [
                            'type' => 'features',
                            'data' => [
                                'title' => 'لماذا تختارنا؟',
                                'items' => [
                                    ['title' => 'شريك موثوق', 'description' => 'نحن شريك موثوق في دفع التحول الرقمي.', 'icon' => 'FaHandshake'],
                                    ['title' => 'حلول مبتكرة', 'description' => 'نقدم حلولاً مبتكرة ومخصصة تدفع نمو الأعمال.', 'icon' => 'FaLightbulb'],
                                    ['title' => 'فريق خبير', 'description' => 'يتكون فريقنا من خبراء ذوي مهارات عالية ومكرسين للتميز.', 'icon' => 'FaUsers'],
                                    ['title' => 'التركيز على العميل', 'description' => 'نضع احتياجات عملائنا في المقام الأول ونضمن توافق كل مشروع مع أهدافهم.', 'icon' => 'FaHeart'],
                                    ['title' => 'منهجية مثبتة', 'description' => 'نستخدم منهجية مثبتة للنجاح، تجمع بين الممارسات الرشيقة والجودة العالية.', 'icon' => 'FaChartLine'],
                                    ['title' => 'دعم مستمر', 'description' => 'نقدم دعماً وصيانة مستمرة لضمان الأداء الأمثل دائماً.', 'icon' => 'FaHeadset'],
                                ]
                            ]
                        ],
                        [
                            'type' => 'team',
                            'data' => [
                                'title' => 'تعرف على المؤسسين',
                                'description' => 'نحن فريق من المحترفين الشغوفين، مكرسون لتحويل أفكارك إلى واقع.',
                                'items' => [
                                    ['name' => 'جون دو', 'role' => 'المدير الإبداعي التنفيذي', 'image' => ''],
                                    ['name' => 'جين سميث', 'role' => 'الرئيس التنفيذي للتكنولوجيا', 'image' => ''],
                                    ['name' => 'أحمد علي', 'role' => 'رئيس قطاع التسويق', 'image' => ''],
                                ]
                            ]
                        ],
                        [
                            'type' => 'cta',
                            'data' => [
                                'title' => 'هل أنت مستعد لنقل عملك إلى المستوى التالي؟',
                                'description' => 'اتصل بنا اليوم لاستكشاف كيف يمكن لحلولنا البرمجية المبتكرة أن تدفع النمو وتبسط العمليات وتطلق العنان لإمكانياتك الكاملة في العصر الرقمي.',
                                'button_text' => 'اتصل بنا',
                                'button_url' => '/contact-us',
                            ]
                        ],
                        [
                            'type' => 'team_work',
                            'data' => [
                                'title' => 'التعاون، والإبداع والابتكار',
                                'description' => 'خلق بيئة تفاعلية تتلاقى فيها الرؤى والأفكار لتقديم حلول برمجية استثنائية.',
                                'pillars' => [
                                    [
                                        'title' => 'التعاون والابتكار',
                                        'description' => 'خلق بيئة تفاعلية تتلاقى فيها الرؤى والأفكار لتقديم حلول برمجية استثنائية.',
                                        'icon' => 'FaUsers'
                                    ],
                                    [
                                        'title' => 'التميز التقني والبرمجي',
                                        'description' => 'الالتزام بكتابة أكواد نظيفة، تصميم بنية تحتية قوية، واستخدام أحدث التقنيات التقنية.',
                                        'icon' => 'FaCode'
                                    ],
                                    [
                                        'title' => 'التصميم ذو الأثر الفعال',
                                        'description' => 'دمج الذوق الإبداعي بمتطلبات تجربة المستخدم لخلق هويات رقمية مذهلة تحقق النتائج.',
                                        'icon' => 'FaPaintBrush'
                                    ]
                                ],
                                'members' => [
                                    [
                                        'name' => 'محمد عبد العال',
                                        'role' => 'المدير الإبداعي التنفيذي وشريك مؤسس',
                                        'dept' => 'management',
                                        'image' => 'pages/person-1.webp',
                                        'linkedin' => 'https://linkedin.com',
                                        'github' => '#',
                                        'twitter' => 'https://x.com'
                                    ],
                                    [
                                        'name' => 'أحمد محمد',
                                        'role' => 'الرئيس التنفيذي للتكنولوجيا وشريك مؤسس',
                                        'dept' => 'management',
                                        'image' => 'pages/person-2.webp',
                                        'linkedin' => 'https://linkedin.com',
                                        'github' => 'https://github.com',
                                        'twitter' => 'https://x.com'
                                    ],
                                    [
                                        'name' => 'أحمد رمضان',
                                        'role' => 'رئيس قطاع التسويق وشريك مؤسس',
                                        'dept' => 'management',
                                        'image' => 'pages/person-3.webp',
                                        'linkedin' => 'https://linkedin.com',
                                        'github' => '#',
                                        'twitter' => 'https://x.com'
                                    ],
                                    [
                                        'name' => 'سارة جينكينز',
                                        'role' => 'رئيسة مصممي واجهة وتجربة المستخدم',
                                        'dept' => 'design',
                                        'image' => 'pages/person-1.webp',
                                        'linkedin' => 'https://linkedin.com',
                                        'github' => '#',
                                        'twitter' => 'https://x.com'
                                    ],
                                    [
                                        'name' => 'ديفيد تشين',
                                        'role' => 'مطور ويب متكامل أول',
                                        'dept' => 'development',
                                        'image' => 'pages/person-2.webp',
                                        'linkedin' => 'https://linkedin.com',
                                        'github' => 'https://github.com',
                                        'twitter' => 'https://x.com'
                                    ],
                                    [
                                        'name' => 'أميرة يوسف',
                                        'role' => 'مهندسة واجهات أمامية أولى',
                                        'dept' => 'development',
                                        'image' => 'pages/person-3.webp',
                                        'linkedin' => 'https://linkedin.com',
                                        'github' => 'https://github.com',
                                        'twitter' => 'https://x.com'
                                    ],
                                    [
                                        'name' => 'كريم محمود',
                                        'role' => 'خبير سيو وتسويق نمو رقمي',
                                        'dept' => 'marketing',
                                        'image' => 'pages/person-2.webp',
                                        'linkedin' => 'https://linkedin.com',
                                        'github' => '#',
                                        'twitter' => 'https://x.com'
                                    ],
                                    [
                                        'name' => 'إيميلي تايلور',
                                        'role' => 'المدير الفني الفني الإبداعي',
                                        'dept' => 'design',
                                        'image' => 'pages/person-1.webp',
                                        'linkedin' => 'https://linkedin.com',
                                        'github' => '#',
                                        'twitter' => 'https://x.com'
                                    ]
                                ]
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

        // Hero settings translations
        \App\Models\Setting::updateOrCreate(
            ['key' => 'hero_title_1'],
            [
                'value' => json_encode(['en' => 'Building The Future', 'ar' => 'بناء المستقبل']),
                'type' => 'text',
                'value_source' => 'link'
            ]
        );
        \App\Models\Setting::updateOrCreate(
            ['key' => 'hero_title_2'],
            [
                'value' => json_encode(['en' => 'Of Your Business', 'ar' => 'لأعمالك']),
                'type' => 'text',
                'value_source' => 'link'
            ]
        );
        \App\Models\Setting::updateOrCreate(
            ['key' => 'hero_desc'],
            [
                'value' => json_encode([
                    'en' => 'We provide state-of-the-art web solutions, from stunning designs to powerful backend systems, helping you dominate the digital galaxy.',
                    'ar' => 'نحن نقدم أحدث حلول الويب، بدءاً من التصاميم المذهلة إلى الأنظمة الخلفية القوية، لمساعدتك في السيطرة على المجرة الرقمية.'
                ]),
                'type' => 'text',
                'value_source' => 'link'
            ]
        );
        \\App\\Models\\Setting::updateOrCreate(
            ['key' => 'company_video_url'],
            [
                'value' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'type' => 'video',
                'value_source' => 'link'
            ]
        );
    }
}
