<?php

namespace Database\Seeders;

use App\Models\NavLink;
use App\Models\Page;
use App\Models\ProjectCategory;
use App\Models\Setting;
use App\Models\TeamMember;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class InitialContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        NavLink::truncate();
        ProjectCategory::truncate();
        TeamMember::truncate();
        Schema::enableForeignKeyConstraints();

        // Project Categories
        ProjectCategory::create(['slug' => 'websites', 'name' => ['en' => 'Websites & Web Apps', 'ar' => 'تصميم وبرمجة مواقع']]);
        ProjectCategory::create(['slug' => 'mobile', 'name' => ['en' => 'Mobile Applications', 'ar' => 'تطبيقات الجوال']]);
        ProjectCategory::create(['slug' => 'motion', 'name' => ['en' => 'Motion Graphics', 'ar' => 'الموشن جرافيك']]);
        ProjectCategory::create(['slug' => 'digital', 'name' => ['en' => 'Digital Identity & Branding', 'ar' => 'الهوية الرقمية']]);
        ProjectCategory::create(['slug' => 'social', 'name' => ['en' => 'Social Media & Marketing', 'ar' => 'سوشيال ميديا وتسويق']]);

        // Navigation Links
        NavLink::create(['url' => '/Home', 'label' => ['en' => 'Home', 'ar' => 'الرئيسية'], 'sort_order' => 1]);
        NavLink::create(['url' => '/about-us', 'label' => ['en' => 'About us', 'ar' => 'عن الشركة'], 'sort_order' => 2]);
        $services = NavLink::create(['url' => '#', 'label' => ['en' => 'Services', 'ar' => 'خدماتنا'], 'sort_order' => 3]);

        NavLink::create(['url' => '/services-web-dev', 'label' => ['en' => 'Web Development', 'ar' => 'برمجة المواقع'], 'parent_id' => $services->id, 'sort_order' => 1]);
        NavLink::create(['url' => '/services-web-design', 'label' => ['en' => 'Web Design', 'ar' => 'تصميم المواقع'], 'parent_id' => $services->id, 'sort_order' => 2]);
        NavLink::create(['url' => '/services-social-media', 'label' => ['en' => 'Social Media', 'ar' => 'إدارة السوشيال ميديا'], 'parent_id' => $services->id, 'sort_order' => 3]);
        NavLink::create(['url' => '/services-web-hosting', 'label' => ['en' => 'Web Hosting', 'ar' => 'استضافة المواقع'], 'parent_id' => $services->id, 'sort_order' => 4]);

        NavLink::create(['url' => '/portfolio', 'label' => ['en' => 'Our Works', 'ar' => 'أعمالنا'], 'sort_order' => 4]);
        NavLink::create(['url' => '/blog', 'label' => ['en' => 'Blog', 'ar' => 'المدونة'], 'sort_order' => 5]);
        NavLink::create(['url' => '/our-products', 'label' => ['en' => 'Our Products', 'ar' => 'منتجاتنا'], 'sort_order' => 6]);
        NavLink::create(['url' => '/careers', 'label' => ['en' => 'Careers', 'ar' => 'الوظائف'], 'sort_order' => 7]);
        NavLink::create(['url' => '/faq', 'label' => ['en' => 'FAQ', 'ar' => 'الأسئلة الشائعة'], 'sort_order' => 8]);
        NavLink::create(['url' => '/contact-us', 'label' => ['en' => 'Contact Us', 'ar' => 'اتصل بنا'], 'sort_order' => 9]);

        // Seed Team Members from serv5.com XML
        $teamFile = database_path('seeders/data/team_members.json');
        if (file_exists($teamFile)) {
            $teams = json_decode((string) file_get_contents($teamFile), true);
            if (is_array($teams)) {
                foreach ($teams as $t) {
                    TeamMember::create([
                        'name' => $t['name'],
                        'role' => $t['role'],
                        'bio' => $t['bio'],
                        'image' => $t['image'],
                        'facebook_url' => 'https://www.facebook.com/serv5com',
                        'linkedin_url' => 'https://www.linkedin.com/company/serv5',
                        'is_active' => true,
                    ]);
                }
            }
        }

        // Pages (populated with real serv5.com XML content from Page 283 AR & Page 11134 EN)
        $pages = [
            [
                'slug' => 'about-us',
                'title' => ['en' => 'About Us', 'ar' => 'عن الشركة'],
                'subtitle' => [
                    'en' => 'Serv5, a 15-year success in Web Design, Programming Websites, Mobile Application Programming, and Digital Marketing.',
                    'ar' => 'سيرف فايف نجاح مستمر منذ عام 2009 في تصميم المواقع، برمجة المواقع، برمجة تطبيقات الجوال، والتسويق الإلكتروني.',
                ],
                'content' => [
                    'en' => 'Serv5, a 15-year success in Web Design, Programming Websites, Mobile Application Programming in addition to Digital Marketing; We are a company that supports development and generates creative ideas in various fields. We did not just strive for excellence, but our first goal was customer satisfaction, so we became the best. Our motto is the ability and adaptability to change, providing new solutions that meet the evolving requirements of our customers since 2009.',
                    'ar' => 'سيرف فايف نجاح دام على مدار أكثر من 15 عاماً. تميزنا في تصميم المواقع، برمجة المواقع، برمجة تطبيقات الجوال علاوة على التسويق الإلكتروني؛ فنحن شركة تدعم التطوير وتعمل على توليد أفكار إبداعية في شتى المجالات. لم نسعَ فقط للتميز بل كان هدفنا الأول هو إرضاء العميل، وشعارنا هو القدرة والتكيف على التغيير لتقديم حلول جديدة تلائم متطلبات عملائنا المتجددة منذ عام 2009.',
                ],
                'sections' => [
                    'en' => [
                        [
                            'type' => 'stats',
                            'data' => [
                                'items' => [
                                    ['label' => 'Since 2009, we bring over 15 years of industry expertise in web design, programming, mobile apps, and digital marketing.', 'value' => '15+ Years Of Experience'],
                                    ['label' => 'Customer satisfaction matters to us and customers\' trust in us and what we offer meets their aspirations.', 'value' => '99% Client Satisfaction'],
                                    ['label' => 'A robust portfolio of over 380 completed websites, mobile apps, and digital campaigns across the Arab region and worldwide.', 'value' => '380+ Successful Projects'],
                                ],
                            ],
                        ],
                        [
                            'type' => 'text_image',
                            'data' => [
                                'title' => 'Our Vision',
                                'tagline' => 'Vision',
                                'content' => "Leadership in creating and developing creative software ideas at the level of the Arab region and the world at large.\n\nWe strive to provide high-quality professional technical software services at competitive prices that exceed customer expectations.",
                                'layout' => 'text_left',
                            ],
                        ],
                        [
                            'type' => 'text_image',
                            'data' => [
                                'title' => 'Our Mission',
                                'tagline' => 'Mission',
                                'content' => "The formation of an entity that pushes the development of the software industry in the region according to the latest scientific methods, and consolidates the idea of creativity in this industry relying on cadres with many years of experience in the software market.",
                                'layout' => 'text_right',
                            ],
                        ],
                        [
                            'type' => 'journey',
                            'data' => [
                                'title' => 'OUR JOURNEY',
                                'description' => 'Over 15 years of growth, innovation, and digital transformation since 2009.',
                                'items' => [
                                    ['year' => '2009', 'location' => 'Egypt', 'users' => 'Launch in Mansoura', 'partnership' => '', 'description' => 'Started Serv5 operations in Egypt with a dedicated team of web designers and software engineers.'],
                                    ['year' => '2012', 'location' => 'Egypt', 'users' => '50+ Active Clients', 'partnership' => '', 'description' => 'Expanded our web hosting, domain services, and custom PHP programming offerings.'],
                                    ['year' => '2015', 'location' => 'Egypt & Saudi Arabia', 'users' => '150+ Active Clients', 'partnership' => 'Gulf Market Expansion', 'description' => 'Entered the Saudi and Gulf markets, delivering e-commerce stores and enterprise portals.'],
                                    ['year' => '2018', 'location' => 'Egypt & Saudi Arabia', 'users' => '250+ Active Clients', 'partnership' => 'SaaS & Ready Scripts', 'description' => 'Launched specialized Serv5 software products (Talabi, My Stadium, Classifieds, Travel & Hotel Booking).'],
                                    ['year' => '2021', 'location' => 'Arab Region & Global', 'users' => '350+ Active Clients', 'partnership' => 'Mobile & Motion Hub', 'description' => 'Scaled cross-platform mobile app development (Flutter, iOS, Android) and motion graphics production.'],
                                    ['year' => '2026', 'location' => 'Global Reach', 'users' => '500+ Active Clients', 'partnership' => 'Full Digital Ecosystem', 'description' => 'Continuing our legacy of delivering high-converting websites, SaaS systems, and SEO/marketing campaigns worldwide.'],
                                ],
                            ],
                        ],
                        [
                            'type' => 'global_reach',
                            'data' => [
                                'title' => 'WE ARE HERE FOR YOU',
                                'description' => 'Welcome to the Serv5 headquarters in Egypt and our regional offices serving clients across the globe.',
                                'items' => [
                                    ['city' => 'Mansoura', 'name' => 'Egypt', 'details' => 'Suez Canal St., above Dr. Rami Pharmacy', 'x' => 59, 'y' => 42],
                                    ['city' => 'Riyadh', 'name' => 'Saudi Arabia', 'details' => 'Regional Operations', 'x' => 63, 'y' => 50],
                                    ['city' => 'Dubai', 'name' => 'UAE', 'details' => 'Gulf Business Hub', 'x' => 66, 'y' => 51],
                                ],
                            ],
                        ],
                        [
                            'type' => 'features',
                            'data' => [
                                'title' => 'Advantages of Doing Business With Us',
                                'items' => [
                                    ['title' => 'Honesty and Commitment', 'description' => 'Integrity and commitment to all work ethics, harnessing the latest and finest information technology with complete honesty in every detail.', 'icon' => 'FaHandshake'],
                                    ['title' => 'Customer Confidence', 'description' => 'Customer satisfaction matters to us; customers\' trust in what we offer meets their aspirations and drives us to be the best.', 'icon' => 'FaHeart'],
                                    ['title' => 'After-Sales Service', 'description' => 'After-sales service is one of our top priorities. The customer has the right to continuous technical support and updates.', 'icon' => 'FaHeadset'],
                                    ['title' => 'Extensive Experience', 'description' => 'Serv5 has an integrated set of technical expertise, effective software solutions, and qualified human talent.', 'icon' => 'FaChartLine'],
                                ],
                            ],
                        ],
                        [
                            'type' => 'team',
                            'data' => [
                                'title' => 'MEET OUR LEADERSHIP',
                                'description' => 'We are a team of passionate professionals dedicated to bringing your ideas to life.',
                                'items' => [
                                    ['name' => 'Ahmed Noman', 'role' => 'Chief Executive Officer (CEO)', 'image' => 'https://serv5.com/wp-content/uploads/2021/10/ CEO.png'],
                                    ['name' => 'Faisal Fahd', 'role' => 'Branch Manager', 'image' => 'https://serv5.com/wp-content/uploads/2020/03/faisal.jpg'],
                                    ['name' => 'Kholoud Elngar', 'role' => 'Marketing Specialist', 'image' => 'https://serv5.com/wp-content/uploads/2020/07/kholoud.jpg'],
                                ],
                            ],
                        ],
                        [
                            'type' => 'cta',
                            'data' => [
                                'title' => 'Ready to take your business to the next level?',
                                'description' => 'Contact us today to explore how Serv5 web design, mobile apps, and digital marketing solutions can grow your business.',
                                'button_text' => 'Contact Us',
                                'button_url' => '/contact-us',
                            ],
                        ],
                        [
                            'type' => 'team_work',
                            'data' => [
                                'title' => 'COLLABORATION, CRAFTSMANSHIP, & CREATIVE IMPACT',
                                'description' => 'Welcome to Serv5 headquarters in Egypt. We are pleased with your presence and dealing with you.',
                                'pillars' => [
                                    [
                                        'title' => 'Honesty & Commitment',
                                        'description' => 'Integrity and commitment to all work ethics, harnessing the latest information technology.',
                                        'icon' => 'FaUsers',
                                    ],
                                    [
                                        'title' => 'Technical Excellence',
                                        'description' => 'Integrated technical expertise, effective software solutions, and high-performance architectures.',
                                        'icon' => 'FaCode',
                                    ],
                                    [
                                        'title' => 'After-Sales Support',
                                        'description' => 'Continuous technical support, maintenance, and updates to keep your business running smoothly.',
                                        'icon' => 'FaPaintBrush',
                                    ],
                                ],
                                'members' => [
                                    [
                                        'name' => 'Ahmed Noman',
                                        'role' => 'CEO & Founder',
                                        'dept' => 'management',
                                        'image' => 'pages/person-1.webp',
                                        'linkedin' => 'https://linkedin.com/company/serv5',
                                        'github' => '#',
                                        'twitter' => 'https://x.com/serv5com',
                                    ],
                                    [
                                        'name' => 'Faisal Fahd',
                                        'role' => 'Branch Manager',
                                        'dept' => 'management',
                                        'image' => 'pages/person-2.webp',
                                        'linkedin' => 'https://linkedin.com/company/serv5',
                                        'github' => '#',
                                        'twitter' => 'https://x.com/serv5com',
                                    ],
                                    [
                                        'name' => 'Kholoud Elngar',
                                        'role' => 'Marketing Specialist',
                                        'dept' => 'marketing',
                                        'image' => 'pages/person-3.webp',
                                        'linkedin' => 'https://linkedin.com/company/serv5',
                                        'github' => '#',
                                        'twitter' => 'https://x.com/serv5com',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'ar' => [
                        [
                            'type' => 'stats',
                            'data' => [
                                'items' => [
                                    ['label' => 'منذ عام 2009، نمتلك أكثر من 15 عاماً من الخبرة في تصميم وبرمجة المواقع وتطبيقات الجوال والتسويق الإلكتروني.', 'value' => '+15 عاماً من الخبرة'],
                                    ['label' => 'رضى العميل يهمنا وثقة العملاء بنا وبما نقدمه يلبي طموحاتهم ويدفعنا لنكون الأفضل.', 'value' => '99% رضا العملاء'],
                                    ['label' => 'سابقة أعمال قوية تضم أكثر من 380 مشروعاً ناجحاً في مصر والسعودية والمنطقة العربية والعالم.', 'value' => '+380 مشروع ناجح'],
                                ],
                            ],
                        ],
                        [
                            'type' => 'text_image',
                            'data' => [
                                'title' => 'رؤيتنا',
                                'tagline' => 'الرؤية',
                                'content' => "الريادة في إبداع وتطوير أفكار برمجية خلاقة على مستوى المنطقة العربية والعالم أجمع.\n\nنسعى لتقديم خدمات برمجية تقنية محترفة عالية الجودة وبأسعار منافسة تتجاوز توقعات العملاء.",
                                'layout' => 'text_left',
                            ],
                        ],
                        [
                            'type' => 'text_image',
                            'data' => [
                                'title' => 'مهمتنا',
                                'tagline' => 'المهمة',
                                'content' => "تكوين كيان يدفع باتجاه تطور صناعة البرمجيات في المنطقة وفق أحدث الطرق العلمية، وترسيخ فكر الإبداع في هذه الصناعة اعتماداً على كوادر ذات خبرة سنوات طويلة بالسوق البرمجية.",
                                'layout' => 'text_right',
                            ],
                        ],
                        [
                            'type' => 'journey',
                            'data' => [
                                'title' => 'مسيرة سيرف فايف',
                                'description' => 'أكثر من 15 عاماً من النجاح والتطوير والابتكار منذ عام 2009.',
                                'items' => [
                                    ['year' => '2009', 'location' => 'مصر', 'users' => 'التأسيس في المنصورة', 'partnership' => '', 'description' => 'انطلاق شركة سيرف فايف في مصر بفريق متخصص في تصميم وبرمجة المواقع الإلكترونية.'],
                                    ['year' => '2012', 'location' => 'مصر', 'users' => '+50 عميلاً نشطاً', 'partnership' => '', 'description' => 'التوسع في خدمات استضافة المواقع وحجز النطاقات والبرمجة الخاصة.'],
                                    ['year' => '2015', 'location' => 'مصر والسعودية', 'users' => '+150 عميلاً نشطاً', 'partnership' => 'التوسع في السوق الخليجي', 'description' => 'دخول السوق السعودي والخليجي وتنفيذ عشرات المتاجر الإلكترونية ومواقع الشركات.'],
                                    ['year' => '2018', 'location' => 'مصر والسعودية', 'users' => '+250 عميلاً نشطاً', 'partnership' => 'إطلاق منتجات واسكربتات سيرف فايف', 'description' => 'إطلاق منتجاتنا البرمجية المتخصصة (اسكربت طلبي، اسكربت ملعبي، حجز الفنادق والطيران، الإعلانات المبوبة).'],
                                    ['year' => '2021', 'location' => 'المنطقة العربية', 'users' => '+350 عميلاً نشطاً', 'partnership' => 'تطبيقات الجوال والموشن جرافيك', 'description' => 'التوسع الكبير في برمجة تطبيقات الجوال (Android & iOS) وإنتاج الموشن جرافيك والتسويق الرقمي.'],
                                    ['year' => '2026', 'location' => 'انتشار عالمي', 'users' => '+500 عميل نشط', 'partnership' => 'منظومة رقمية متكاملة', 'description' => 'مواصلة الريادة بتقديم أحدث الحلول البرمجية والأنظمة السحابية وخدمات السيو والتسويق الإلكتروني.'],
                                ],
                            ],
                        ],
                        [
                            'type' => 'global_reach',
                            'data' => [
                                'title' => 'أهلاً بك في مقر سيرف فايف',
                                'description' => 'أهلاً بك معنا في مقر سيرف فايف في مصر، نسعد بوجودكم وبالتعامل معكم في جميع أنحاء الوطن العربي والعالم.',
                                'items' => [
                                    ['city' => 'المنصورة', 'name' => 'مصر', 'details' => 'شارع قناة السويس أعلى صيدلية د. رامي', 'x' => 59, 'y' => 42],
                                    ['city' => 'الرياض', 'name' => 'المملكة العربية السعودية', 'details' => 'إدارة العمليات الإقليمية', 'x' => 63, 'y' => 50],
                                    ['city' => 'دبي', 'name' => 'الإمارات', 'details' => 'مركز تطوير الأعمال', 'x' => 66, 'y' => 51],
                                ],
                            ],
                        ],
                        [
                            'type' => 'features',
                            'data' => [
                                'title' => 'مميزات التعامل معنا',
                                'items' => [
                                    ['title' => 'الأمانة والالتزام', 'description' => 'النزاهة والاستقامة والالتزام بكل أدبيات العمل، وتسخير أحدث وأرقى التقنيات المعلوماتية، والأمانة الكاملة في كل صغيرة وكبيرة.', 'icon' => 'FaHandshake'],
                                    ['title' => 'ثقة العملاء', 'description' => 'رضى العميل يهمنا وثقة العملاء بنا وبما نقدمه يلبي طموحاتهم، وتوجهنا لنكون الأفضل بين جميع المنافسين بتعدد خدماتنا وجودتها.', 'icon' => 'FaHeart'],
                                    ['title' => 'خدمات ما بعد البيع', 'description' => 'خدمة ما بعد البيع من أولوياتنا، للعميل حق الدعم الفني والتعديل على متطلباته وتنفيذ ما هو متوافق مع الموجود.', 'icon' => 'FaHeadset'],
                                    ['title' => 'الخبرة الواسعة', 'description' => 'تتوافر لدى سيرف فايف مجموعة متكاملة من الخبرات التقنية والحلول الفعالة والخبرات البشرية ويظهر ذلك من خلال الأداء المتميز لنا.', 'icon' => 'FaChartLine'],
                                ],
                            ],
                        ],
                        [
                            'type' => 'team',
                            'data' => [
                                'title' => 'قيادة وفريق سيرف فايف',
                                'description' => 'نحن فريق من المحترفين الشغوفين، مكرسون لتحويل أفكارك إلى واقع برمجي وتسويقي ناجح.',
                                'items' => [
                                    ['name' => 'أحمد نعمان', 'role' => 'الرئيس التنفيذي (CEO)', 'image' => 'https://serv5.com/wp-content/uploads/2021/10/ CEO.png'],
                                    ['name' => 'فيصل فهد', 'role' => 'مدير الفرع', 'image' => 'https://serv5.com/wp-content/uploads/2020/03/faisal.jpg'],
                                    ['name' => 'خلود النجار', 'role' => 'أخصائية التسويق الإلكتروني', 'image' => 'https://serv5.com/wp-content/uploads/2020/07/kholoud.jpg'],
                                ],
                            ],
                        ],
                        [
                            'type' => 'cta',
                            'data' => [
                                'title' => 'هل أنت مستعد لنقل عملك إلى المستوى التالي مع سيرف فايف؟',
                                'description' => 'تواصل معنا اليوم للحصول على استشارة مجانية وعرض سعر مخصص لتصميم موقعك أو تطبيقك أو حملتك التسويقية.',
                                'button_text' => 'اتصل بنا الآن',
                                'button_url' => '/contact-us',
                            ],
                        ],
                        [
                            'type' => 'team_work',
                            'data' => [
                                'title' => 'التعاون، الإبداع، والالتزام بالجودة',
                                'description' => 'أهلاً بك معنا في مقر سيرف فايف في مصر، نسعد بوجودكم وبالتعامل معكم لتقديم أفضل الحلول الرقمية.',
                                'pillars' => [
                                    [
                                        'title' => 'الأمانة والالتزام',
                                        'description' => 'النزاهة والاستقامة والالتزام بكل أدبيات العمل وتسخير أحدث التقنيات المعلوماتية.',
                                        'icon' => 'FaUsers',
                                    ],
                                    [
                                        'title' => 'الخبرة الواسعة',
                                        'description' => 'مجموعة متكاملة من الخبرات التقنية والحلول البرمجية الفعالة والكوادر البشرية المحترفة.',
                                        'icon' => 'FaCode',
                                    ],
                                    [
                                        'title' => 'خدمات ما بعد البيع',
                                        'description' => 'دعم فني متواصل على مدار الساعة وتحديثات مستمرة لضمان استقرار ونمو مشروعك.',
                                        'icon' => 'FaPaintBrush',
                                    ],
                                ],
                                'members' => [
                                    [
                                        'name' => 'أحمد نعمان',
                                        'role' => 'الرئيس التنفيذي وشريك مؤسس (CEO)',
                                        'dept' => 'management',
                                        'image' => 'pages/person-1.webp',
                                        'linkedin' => 'https://linkedin.com/company/serv5',
                                        'github' => '#',
                                        'twitter' => 'https://x.com/serv5com',
                                    ],
                                    [
                                        'name' => 'فيصل فهد',
                                        'role' => 'مدير الفرع',
                                        'dept' => 'management',
                                        'image' => 'pages/person-2.webp',
                                        'linkedin' => 'https://linkedin.com/company/serv5',
                                        'github' => '#',
                                        'twitter' => 'https://x.com/serv5com',
                                    ],
                                    [
                                        'name' => 'خلود النجار',
                                        'role' => 'أخصائية التسويق الإلكتروني',
                                        'dept' => 'marketing',
                                        'image' => 'pages/person-3.webp',
                                        'linkedin' => 'https://linkedin.com/company/serv5',
                                        'github' => '#',
                                        'twitter' => 'https://x.com/serv5com',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'slug' => 'privacy-policy',
                'title' => ['en' => 'Privacy Policy', 'ar' => 'سياسة الخصوصية'],
                'subtitle' => [
                    'en' => 'At Serv5, the privacy of our visitors is of extreme importance to us.',
                    'ar' => 'في سيرف فايف، خصوصية زوارنا وعملائنا لها أهمية بالغة بالنسبة لنا.',
                ],
                'content' => [
                    'en' => "Privacy Policy for Serv5:\nAt Serv5, the privacy of our visitors is of extreme importance to us. This document outlines the privacy policy for the types of personal information that Serv5 collects and receives, and how it is used.\n\nLog Files:\nLike many other Web sites, Serv5 makes use of log files. The information inside the log files includes internet protocol (IP) addresses, type of browser, Internet Service Provider (ISP), date/time stamp, referring/exit pages, and number of clicks to analyze trends, administer the site, track user's movement around the site, and gather demographic information. IP addresses and other such information are not linked to any information that is personally identifiable.",
                    'ar' => "سياسة الخصوصية لـ Serv5:\nإذا كنت بحاجة إلى مزيد من المعلومات أو لديك أية أسئلة عن سياسة الخصوصية، لا تتردد في الاتصال بنا.\nفي سيرف فايف، خصوصية زوارنا لها أهمية بالغة بالنسبة لنا. توجز هذه الوثيقة سياسة الخصوصية لأنواع المعلومات الشخصية التي تتلقاها سيرف فايف وكيفية استخدامها.\n\nملفات الدخول:\nمثل العديد من مواقع الويب الأخرى، تستخدم سيرف فايف ملفات السجل. المعلومات داخل ملفات الدخول تشمل عناوين بروتوكول الإنترنت (IP)، نوع المتصفح، مزود خدمة الإنترنت (ISP)، التاريخ/الوقت، صفحات الإحالة/الخروج، وعدد النقرات لتحليل الاتجاهات وإدارة الموقع.",
                ],
            ],
            [
                'slug' => 'contact-us',
                'title' => ['en' => 'Contact Us', 'ar' => 'اتصل بنا'],
                'subtitle' => [
                    'en' => 'Get in touch with Serv5 for web design, mobile apps, hosting, or marketing inquiries (00201288863631).',
                    'ar' => 'تواصل مع شركة سيرف فايف لتصميم وبرمجة المواقع وتطبيقات الجوال والتسويق الإلكتروني (00201288863631).',
                ],
            ],
            [
                'slug' => 'portfolio',
                'title' => ['en' => 'Our Portfolio', 'ar' => 'سابقة أعمالنا'],
                'subtitle' => [
                    'en' => 'Explore our latest websites, mobile applications, motion graphics, and digital marketing projects.',
                    'ar' => 'استكشف أحدث أعمالنا في تصميم وبرمجة المواقع وتطبيقات الجوال والموشن جرافيك والهوية الرقمية.',
                ],
            ],
            [
                'slug' => 'faq',
                'title' => ['en' => 'Frequently Asked Questions', 'ar' => 'الأسئلة الشائعة'],
                'subtitle' => [
                    'en' => 'Find answers to common questions about Serv5 web design, hosting, mobile apps, and support services.',
                    'ar' => 'إجابات وافية لأهم الأسئلة الشائعة حول خدمات شركة سيرف فايف لتصميم وبرمجة المواقع والدعم الفني.',
                ],
            ],
            [
                'slug' => 'home',
                'title' => ['en' => 'Serv5 | Web Design, Programming & Digital Marketing', 'ar' => 'سيرف فايف | شركة تصميم وبرمجة المواقع وخدمات التسويق الإلكتروني'],
                'subtitle' => [
                    'en' => 'Your right destination for professional website design, mobile app programming, and digital marketing.',
                    'ar' => 'وجهتك الصحيحة للحصول على خدمات تصميم وبرمجة المواقع وتطبيقات الجوال والتسويق الإلكتروني المتميز.',
                ],
            ],
            [
                'slug' => 'blog',
                'title' => ['en' => 'Serv5 Blog', 'ar' => 'مدونة سيرف فايف'],
                'subtitle' => [
                    'en' => 'Discover articles, guides, and insights on web design, programming, mobile apps, SEO, and digital marketing.',
                    'ar' => 'اكتشف أحدث المقالات والشروحات في مجالات تصميم وبرمجة المواقع، المتاجر الإلكترونية، تطبيقات الجوال، والتسويق الرقمي.',
                ],
            ],
            [
                'slug' => 'services-web-dev',
                'title' => ['en' => 'Web Development', 'ar' => 'برمجة وتطوير المواقع'],
                'subtitle' => [
                    'en' => 'Programming strong websites without loopholes using the latest technologies.',
                    'ar' => 'شركة برمجة مواقع احترافية خالية من الثغرات بأحدث التقنيات البرمجية.',
                ],
            ],
        ];

        foreach ($pages as $page) {
            Page::updateOrCreate(['slug' => $page['slug']], $page);
        }

        // Hero settings translations (from serv5.com XML)
        Setting::updateOrCreate(
            ['key' => 'hero_title_1'],
            [
                'value' => json_encode(['en' => 'Serv5 Digital Solutions', 'ar' => 'سيرف فايف للحلول الرقمية'], JSON_UNESCAPED_UNICODE),
                'type' => 'text',
                'value_source' => 'link',
            ]
        );
        Setting::updateOrCreate(
            ['key' => 'hero_title_2'],
            [
                'value' => json_encode(['en' => 'Web, Mobile & Marketing', 'ar' => 'تصميم وبرمجة المواقع والتطبيقات'], JSON_UNESCAPED_UNICODE),
                'type' => 'text',
                'value_source' => 'link',
            ]
        );
        Setting::updateOrCreate(
            ['key' => 'hero_desc'],
            [
                'value' => json_encode([
                    'en' => 'You have reached your right destination. With Serv5 you get professional website design, custom web & mobile application programming, cloud hosting, and distinguished digital marketing services.',
                    'ar' => 'لقد وصلت إلى وجهتك الصحيحة، ما تبحث عنه موجود هنا. معنا ستحصل على خدمات تصميم وبرمجة المواقع، برمجة تطبيقات الجوال، استضافة المواقع، وخدمات التسويق الإلكتروني المتميزة.',
                ], JSON_UNESCAPED_UNICODE),
                'type' => 'text',
                'value_source' => 'link',
            ]
        );
        Setting::updateOrCreate(
            ['key' => 'company_video_url'],
            [
                'value' => 'https://www.youtube.com/watch?v=8szViOmNwBw',
                'type' => 'video',
                'value_source' => 'link',
            ]
        );
    }
}
