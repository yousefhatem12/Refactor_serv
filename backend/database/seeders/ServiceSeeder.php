<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Web Hosting & Cloud Hosting Blocks (7 Blocks)
        $webHostingBlocks = [
            [
                'type' => 'hero',
                'data' => [
                    'title' => ['en' => 'Premium Cloud Web Hosting Services', 'ar' => 'استضافة سحابية فائقة السرعة والأداء'],
                    'subtitle' => [
                        'en' => 'Deploy your business applications on our enterprise-grade cloud servers. Experience instant NVMe SSD storage speeds, daily automatic backups, and DDoS protection with an uptime guarantee of 99.99%.',
                        'ar' => 'انشر تطبيقات أعمالك وموقعك على خوادمنا السحابية فائقة السرعة والموثوقية. استمتع بوحدات تخزين NVMe SSD فائقة الأداء، ونقاط استعادة يومية، مع حماية أمنية متكاملة تضمن بقاء موقعك متاحاً طوال الوقت.'
                    ],
                    'hero_image' => 'services/heroes/hosting-hero.webp',
                ]
            ],
            [
                'type' => 'features',
                'data' => [
                    'title' => ['en' => 'High-Performance Web Hosting Capabilities', 'ar' => 'قدرات استضافة مواقع من الجيل القادم'],
                    'subtitle' => ['en' => 'We utilize advanced hardware architecture to provide unmatched server speed and stability.', 'ar' => 'نعتمد على أحدث التقنيات والعتاد البرمجي لتقديم خوادم مستقرة وسريعة تلبي طموحاتك.'],
                    'items' => [
                        [
                            'title' => ['en' => 'NVMe SSD Storage', 'ar' => 'تخزين NVMe SSD فائق السرعة'],
                            'desc' => ['en' => 'Accelerate database queries and page loading times up to 10x faster than standard SSD drives.', 'ar' => 'سرع استعلامات قواعد البيانات والصفحات حتى 10 مرات مقارنة بالأقراص التقليدية.']
                        ],
                        [
                            'title' => ['en' => 'LiteSpeed Web Server', 'ar' => 'خوازم ويب LiteSpeed المتطورة'],
                            'desc' => ['en' => 'Supercharge web requests with built-in caching systems for WordPress, Laravel, and custom sites.', 'ar' => 'ضاعف سرعة تحميل موقعك مع نظام تخزين مؤقت متكامل وذكي لمختلف المنصات.']
                        ],
                        [
                            'title' => ['en' => 'Free Wildcard SSL', 'ar' => 'شهادات SSL مجانية غير محدودة'],
                            'desc' => ['en' => 'Secure your main domain and all subdomains with automated Let\'s Encrypt certificates.', 'ar' => 'أمن نطاقك الرئيسي ونطاقاتك الفرعية بالكامل بشهادات تشفير معتمدة وتلقائية.']
                        ],
                        [
                            'title' => ['en' => 'Automated Daily Backups', 'ar' => 'نسخ احتياطي يومي تلقائي خارجي'],
                            'desc' => ['en' => 'Never worry about data loss; we capture daily recovery states and store them on remote servers.', 'ar' => 'لا تقلق بشأن فقدان بياناتك؛ نقوم بالنسخ الاحتياطي يومياً ونحتفظ به في خوادم منفصلة.']
                        ],
                        [
                            'title' => ['en' => 'DDoS Shield Protection', 'ar' => 'حماية متقدمة ضد هجمات DDoS'],
                            'desc' => ['en' => 'Multi-layered firewall filtering to block malicious traffic spikes and keep your site online.', 'ar' => 'جدار حماية ذكي يصد الهجمات وحركات المرور الضارة تلقائياً ليبقى موقعك متاحاً.']
                        ],
                        [
                            'title' => ['en' => '24/7 Expert Tech Support', 'ar' => 'دعم فني متخصص على مدار الساعة'],
                            'desc' => ['en' => 'Our system administrators are online 24/7 to resolve server queries and technical issues instantly.', 'ar' => 'فريقنا من مهندسي النظام متواجد دائماً لحل أي مشاكل أو استفسارات فنية فوراً.']
                        ]
                    ]
                ]
            ],
            [
                'type' => 'hosting_offers',
                'data' => [
                    'title' => ['en' => 'Choose Your Business Hosting Plan', 'ar' => 'اختر باقة الاستضافة المثالية لأعمالك'],
                    'items' => [
                        [
                            'title' => ['en' => 'Starter Cloud', 'ar' => 'الباقة السحابية للمبتدئين'],
                            'desc' => [
                                'en' => "Price: 39 SAR\nPeriod: / month\nSubtitle: Ideal for blogs and personal portfolios\n10 GB NVMe Storage\n1 Hosted Website\nFree SSL Certificate\n10 Email Accounts\nFree Domain for 1st Year\nStandard cPanel Control Panel",
                                'ar' => "Price: 39 ر.س\nPeriod: / شهرياً\nSubtitle: مثالي للمدونات والمواقع التعريفية الناشئة\nمساحة تخزين 10 جيجابايت NVMe\nاستضافة موقع واحد فقط\nشهادة SSL مجانية\n10 حسابات بريد إلكتروني\nدومين مجاني للسنة الأولى\nلوحة تحكم cPanel القياسية"
                            ]
                        ],
                        [
                            'title' => ['en' => 'Professional Business', 'ar' => 'باقة الشركات الاحترافية'],
                            'desc' => [
                                'en' => "Price: 119 SAR\nPeriod: / month\nSubtitle: Best for growing companies and online stores\n50 GB NVMe Storage\n5 Hosted Websites\nDaily Backups (15 Days History)\nUnlimited Databases & Emails\nFree Domain & Dedicated IP\nLiteSpeed Cache Optimization\nPriority Support",
                                'ar' => "Price: 119 ر.س\nPeriod: / شهرياً\nSubtitle: الأفضل للشركات المتوسطة والمتاجر الإلكترونية المتنامية\nمساحة تخزين 50 جيجابايت NVMe\nاستضافة حتى 5 مواقع\nنسخ احتياطي يومي (الاحتفاظ بـ 15 يوماً)\nقواعد بيانات وحسابات بريد غير محدودة\nدومين مجاني وعنوان IP مخصص\nتسريع وتخزين LiteSpeed الذكي\nدعم فني ذو أولوية"
                            ]
                        ],
                        [
                            'title' => ['en' => 'Enterprise Elite', 'ar' => 'باقة النخبة للمؤسسات والشركات الكبرى'],
                            'desc' => [
                                'en' => "Price: 289 SAR\nPeriod: / month\nSubtitle: For high-traffic applications & large portals\nUnlimited NVMe Storage\nUnlimited Websites\nHourly Database Backups\nCustom Server Firewall\nFree Migration Service\nDedicated Manager\nVIP 24/7 Tech Support",
                                'ar' => "Price: 289 ر.س\nPeriod: / شهرياً\nSubtitle: للتطبيقات والمنصات ذات حركة المرور العالية والضخمة\nمساحة تخزين غير محدودة NVMe\nاستضافة عدد غير محدود من المواقع\nنسخ احتياطي لقواعد البيانات كل ساعة\nجدار حماية مخصص للخادم\nخدمة نقل مجانية بالكامل ومؤمنة\nمدير حساب مخصص لمشروعك\nدعم فني لكبار الشخصيات 24/7"
                            ]
                        ]
                    ]
                ]
            ],
            [
                'type' => 'split_content',
                'data' => [
                    'title' => ['en' => 'Zero-Downtime Migration Guarantee', 'ar' => 'ضمان نقل موقعك بدون أي توقف'],
                    'content' => [
                        'en' => 'Are you hosting somewhere else? Our system engineers will transfer your files, databases, and emails to our servers free of charge. We handle the technical details while you focus on your business, ensuring zero downtime and no data loss.',
                        'ar' => 'هل موقعك مستضاف لدى شركة أخرى؟ سيقوم مهندسو الأنظمة لدينا بنقل ملفاتك وقواعد بياناتك وحسابات بريدك الإلكتروني إلى خوادمنا مجاناً بالكامل. نتولى التفاصيل الفنية المعقدة بينما تركز أنت على أعمالك، مع ضمان عدم توقف موقعك لثانية واحدة.'
                    ],
                    'image' => 'services/content/section-2-image.webp',
                    'image_on_right' => true,
                    'items' => [
                        ['title' => ['en' => 'Free database migration', 'ar' => 'نقل قواعد البيانات والملفات مجاناً']],
                        ['title' => ['en' => 'DNS configuration support', 'ar' => 'إعدادات نطاقات DNS وتوجيهها بدقة']],
                        ['title' => ['en' => 'Speed optimization check', 'ar' => 'فحص وتحسين أداء وسرعة الموقع بعد النقل']]
                    ]
                ]
            ],
            [
                'type' => 'why_choose_us',
                'data' => [
                    'title' => ['en' => 'Why Thousands Trust Serv5 Web Hosting', 'ar' => 'لماذا يثق الآلاف باستضافة سيرف فايف؟'],
                    'subtitle' => ['en' => 'Security, speed, and continuous support are the cornerstones of our hosting services.', 'ar' => 'الأمان، والسرعة الفائقة، والدعم الفني المستمر هي الركائز الأساسية لخدمات الاستضافة لدينا.'],
                    'items' => [
                        [
                            'title' => ['en' => '99.99% Uptime Guarantee', 'ar' => 'ضمان تشغيل بنسبة 99.99%'],
                            'desc' => ['en' => 'Redundant network infrastructure to keep your site live around the clock.', 'ar' => 'بنية تحتية احتياطية تضمن بقاء موقعك متاحاً دون انقطاع.']
                        ],
                        [
                            'title' => ['en' => 'Scalable Cloud Nodes', 'ar' => 'موارد خادم مرنة وقابلة للتوسع'],
                            'desc' => ['en' => 'Easily upgrade your processor and RAM resources as your business grows.', 'ar' => 'قم بترقية موارد المعالج والذاكرة العشوائية للخادم بمرونة مع نمو حجم أعمالك.']
                        ],
                        [
                            'title' => ['en' => 'Proactive Security Patching', 'ar' => 'سد الثغرات والتحديثات الأمنية'],
                            'desc' => ['en' => 'Automated server firewalls and vulnerability scans to block attacks.', 'ar' => 'جدران حماية وفحص استباقي لمنع وحظر الاختراقات والهجمات الضارة.']
                        ],
                        [
                            'title' => ['en' => 'Multi-Channel Tech Support', 'ar' => 'دعم فني متعدد القنوات والمنافذ'],
                            'desc' => ['en' => 'Reach out via tickets, live chat, or phone for instant problem resolution.', 'ar' => 'تواصل معنا مباشرة عبر التذاكر، المحادثة الحية، أو الهاتف لحل مشكلتك.']
                        ]
                    ]
                ]
            ],
            [
                'type' => 'blogs',
                'data' => []
            ],
            [
                'type' => 'contact_request',
                'data' => [
                    'style' => 'social_media'
                ]
            ]
        ];

        // 2. Web Design & UI/UX Design & Digital Identity Blocks (7 Blocks)
        $webDesignBlocks = [
            [
                'type' => 'hero',
                'data' => [
                    'title' => ['en' => 'Creative & Interactive Web Design Services', 'ar' => 'تصميم مواقع ويب إبداعي وتفاعلي متميز'],
                    'subtitle' => [
                        'en' => 'We design beautiful, modern, and high-converting websites that express your brand identity. Tailored user experiences that engage visitors and convert them into loyal clients.',
                        'ar' => 'نصمم مواقع ويب جذابة وعصرية ومخصصة لزيادة المبيعات تعكس تفاصيل هويتك التجارية. نصنع تجربة مستخدم فريدة تأسر الزوار وتحولهم إلى عملاء دائمين.'
                    ],
                    'hero_image' => 'services/heroes/web-design-hero.webp',
                ]
            ],
            [
                'type' => 'features',
                'data' => [
                    'title' => ['en' => 'Outstanding Web Design Standards', 'ar' => 'معايير تصميم الويب الممتازة لدينا'],
                    'subtitle' => ['en' => 'Every website we design is tailored to modern visual and technical guidelines.', 'ar' => 'كل موقع نصممه نصنعه خصيصاً ليتناسب مع المعايير البصرية والفنية العالمية الحديثة.'],
                    'items' => [
                        [
                            'title' => ['en' => 'Tailored UI/UX Layouts', 'ar' => 'تصاميم واجهات وتجربة مستخدم مخصصة'],
                            'desc' => ['en' => 'Unique designs built from scratch based on user psychology. No generic templates.', 'ar' => 'تصاميم فريدة مبنية بالكامل من الصفر بناء على علم نفس الزائر ومجال عملك.']
                        ],
                        [
                            'title' => ['en' => 'Fully Responsive Layouts', 'ar' => 'متوافق تماماً مع جميع الشاشات والأجهزة'],
                            'desc' => ['en' => 'Your site will look flawless on smartphones, tablets, laptops, and wide screens.', 'ar' => 'سيبدو موقعك رائعاً وسلساً وسريعاً على الهواتف والتابلت والشاشات الكبيرة.']
                        ],
                        [
                            'title' => ['en' => 'Interactive Micro-Animations', 'ar' => 'حركات بصرية وتأثيرات تفاعلية دقيقة'],
                            'desc' => ['en' => 'Smooth page transitions and interactive hover effects to engage visitors.', 'ar' => 'انتقالات وتأثيرات بصرية ناعمة تزيد من تفاعل الزوار مع محتوى موقعك.']
                        ],
                        [
                            'title' => ['en' => 'SEO & Speed-Friendly', 'ar' => 'بنية مهيأة لمحركات البحث وسرعة التصفح'],
                            'desc' => ['en' => 'Clean layout structures optimized for search visibility and fast loading.', 'ar' => 'توزيع مثالي للعناصر وأكواد خفيفة الوزن لتسريع التحميل والتأهل للنتائج الأولى.']
                        ],
                        [
                            'title' => ['en' => 'Modern Typography & Grids', 'ar' => 'خطوط عصرية وتوزيع شبكي مثالي'],
                            'desc' => ['en' => 'Curated modern font pairings and pixel-perfect layouts for clean readability.', 'ar' => 'تنسيق خطوط راقية وشبكات بصرية دقيقة ومريحة للعين لتسهيل القراءة.']
                        ],
                        [
                            'title' => ['en' => 'Brand Asset Integration', 'ar' => 'دمج متكامل لعناصر علامتك التجارية'],
                            'desc' => ['en' => 'We align the design colors, logos, and voice exactly with your corporate guidelines.', 'ar' => 'نوظف ألوان علامتك التجارية وشعارك ورسالتك بتناغم كامل في كافة الأقسام.']
                        ]
                    ]
                ]
            ],
            [
                'type' => 'portfolio',
                'data' => [
                    'section_title' => ['en' => 'Our Design Masterpieces', 'ar' => 'روائع أعمالنا البصرية'],
                    'is_mobile_app' => false,
                ]
            ],
            [
                'type' => 'split_content',
                'data' => [
                    'title' => ['en' => 'Perfect Mobile Experience First', 'ar' => 'تجربة الهاتف المحمول أولاً دائماً'],
                    'content' => [
                        'en' => 'Over 70% of web traffic comes from mobile devices. That is why our design philosophy is Mobile-First. We make sure that your navigation menus, images, texts, and call-to-actions adapt beautifully to smaller screens, providing an effortless experience for smartphone users.',
                        'ar' => 'أكثر من 70% من زوار مواقع الويب يأتون من الهواتف المحمولة. لذلك فإن فلسفتنا في التصميم تركز على الهاتف أولاً. نحن نضمن أن قوائم التصفح، والصور، والنصوص، وأزرار طلب الخدمة تتكيف بشكل جميل مع الشاشات الصغيرة، مما يوفر تجربة تصفح سلسة وسهلة لمستخدمي الهواتف الذكية.'
                    ],
                    'image' => 'services/content/section-2-image.webp',
                    'image_on_right' => true,
                    'items' => [
                        ['title' => ['en' => 'Dynamic responsive scaling', 'ar' => 'تحجيم متجاوب وديناميكي بالكامل']],
                        ['title' => ['en' => 'Fast touch-friendly buttons', 'ar' => 'أزرار تفاعلية سريعة مناسبة للمس']],
                        ['title' => ['en' => 'Optimized image compression', 'ar' => 'ضغط احترافي للصور لتسريع التصفح']]
                    ]
                ]
            ],
            [
                'type' => 'how_we_work_steps',
                'data' => [
                    'items' => [
                        [
                            'title' => ['en' => 'Step 1: Wireframes', 'ar' => 'الخطوة الأولى: التخطيط الهيكلي والتفاصيل'],
                            'desc' => ['en' => 'Drafting structural layout to define user paths, conversions, and sections.', 'ar' => 'رسم الهيكل العام المبدئي للموقع لتحديد مسارات الزوار وتوزيع السكاشن.']
                        ],
                        [
                            'title' => ['en' => 'Step 2: UI Visual Design', 'ar' => 'الخطوة الثانية: تصميم الواجهات الإبداعي'],
                            'desc' => ['en' => 'Designing complete mockups with colors, typography, assets, and branding.', 'ar' => 'تصميم النماذج الكاملة بالألوان والخطوط والصور لترى كيف سيبدو الموقع بدقة.']
                        ],
                        [
                            'title' => ['en' => 'Step 3: Client Iterations', 'ar' => 'الخطوة الثالثة: المراجعة والتعديل والتطوير'],
                            'desc' => ['en' => 'Gathering your feedback to adjust details and achieve visual perfection.', 'ar' => 'جمع ملاحظاتك وتعديل التفاصيل للوصول للتصميم المثالي الذي يلبي طموحاتك.']
                        ],
                        [
                            'title' => ['en' => 'Step 4: Design Assets Delivery', 'ar' => 'الخطوة الرابعة: تصدير الأصول والتسليم'],
                            'desc' => ['en' => 'Exporting clean visual codes and layered assets ready for development.', 'ar' => 'تصدير كافة الأصول المرئية والملفات بجودة عالية وجاهزة للبرمجة والإطلاق.']
                        ]
                    ]
                ]
            ],
            [
                'type' => 'why_choose_us',
                'data' => [
                    'title' => ['en' => 'Why Design Your Website with Serv5?', 'ar' => 'لماذا تصمم موقعك الإلكتروني معنا؟'],
                    'subtitle' => ['en' => 'We combine visual art with data and technology to create highly effective online experiences.', 'ar' => 'نمزج الفن البصري بالبيانات والتقنية الحديثة لصناعة تجارب رقمية فعالة للغاية.'],
                    'items' => [
                        [
                            'title' => ['en' => '100% Unique Design layouts', 'ar' => 'تصاميم فريدة ومبتكرة بالكامل'],
                            'desc' => ['en' => 'Every project receives a custom mockup tailored strictly to its business identity.', 'ar' => 'كل مشروع يحصل على واجهات مخصصة تماماً تبرز هويته وتجعله فريداً بين منافسيه.']
                        ],
                        [
                            'title' => ['en' => 'User-Centric Navigation Flow', 'ar' => 'تنقل سهل ومريح للمستخدم'],
                            'desc' => ['en' => 'We design with user convenience in mind to increase retention and lower bounce rates.', 'ar' => 'نصمم بنية تصفح ذكية تسهل على الزائر العثور على ما يريد بأقل عدد نقرات.']
                        ],
                        [
                            'title' => ['en' => 'Optimized Call to Actions', 'ar' => 'أزرار طلب خدمة محسنة للتحويل'],
                            'desc' => ['en' => 'Strategically placed CTAs to maximize contact form submissions and quote requests.', 'ar' => 'توزيع ذكي لأزرار الاتصال وطلب الخدمة لتحقيق أعلى نسبة تفاعل ومبيعات.']
                        ],
                        [
                            'title' => ['en' => 'Modern Interaction Details', 'ar' => 'تأثيرات وتفاعلات بصرية حديثة'],
                            'desc' => ['en' => 'Subtle hover transformations and scrolling animations that bring the page to life.', 'ar' => 'حركات ديناميكية وتأثيرات سلسة تعزز تجربة الزائر وتجعل الموقع حيوياً ومتفاعلاً.']
                        ]
                    ]
                ]
            ],
            [
                'type' => 'contact_request',
                'data' => [
                    'style' => 'web_design'
                ]
            ]
        ];

        // 3. Web Development Blocks (6 Blocks)
        $webDevBlocks = [
            [
                'type' => 'hero',
                'data' => [
                    'title' => ['en' => 'Custom Web Application Development Services', 'ar' => 'تطوير تطبيقات الويب المخصصة والمنصات الذكية'],
                    'subtitle' => [
                        'en' => 'Build high-performance, secure, and scalable web applications tailored to your exact business specifications. We turn complex requirements into clean, future-proof code.',
                        'ar' => 'ابنِ تطبيقات ويب متطورة وآمنة وقابلة للتوسع تناسب متطلبات أعمالك بدقة متناهية. نحول المتطلبات المعقدة إلى أكواد برمجية نظيفة وجاهزة للمستقبل.'
                    ],
                    'hero_image' => 'services/heroes/web-dev-hero.webp',
                ]
            ],
            [
                'type' => 'features',
                'data' => [
                    'title' => ['en' => 'Robust Software Development Capabilities', 'ar' => 'قدرات برمجية وهندسة متطورة'],
                    'subtitle' => ['en' => 'We deploy modern technologies to construct high-performance database-driven platforms.', 'ar' => 'نستخدم أحدث التقنيات البرمجية لبناء منصات قوية مدعومة بقواعد بيانات مستقرة وسريعة.'],
                    'items' => [
                        [
                            'title' => ['en' => 'Laravel & NodeJS Core', 'ar' => 'تطوير بلارافيل ونود جي إس'],
                            'desc' => ['en' => 'We code robust backend servers utilizing industry-leading frameworks for stability and performance.', 'ar' => 'نبرمج خوادم ويب خلفية متطورة ومستقرة تعتمد على أفضل بيئات التطوير العالمية.']
                        ],
                        [
                            'title' => ['en' => 'SPA Frontend Architectures', 'ar' => 'واجهات تفاعلية أحادية الصفحة SPA'],
                            'desc' => ['en' => 'Building lightning-fast frontends using React, Next.js, and Vue for seamless page loads.', 'ar' => 'واجهات أمامية فائقة السرعة تعتمد على Next.js وReact لتوفير تجربة تصفح فورية.']
                        ],
                        [
                            'title' => ['en' => 'Secure RESTful APIs', 'ar' => 'واجهات برمجية RESTful آمنة'],
                            'desc' => ['en' => 'Developing clean, documented, and secure API gateways for third-party integrations and apps.', 'ar' => 'بناء وتطوير بوابات ربط API نظيفة ومحمية لتسهيل التكامل مع التطبيقات والأنظمة.']
                        ],
                        [
                            'title' => ['en' => 'Relational & NoSQL Databases', 'ar' => 'قواعد بيانات علائقية وغير علائقية'],
                            'desc' => ['en' => 'Expert database query optimizations utilizing MySQL, PostgreSQL, or MongoDB.', 'ar' => 'تنظيم وتصميم وتطوير قواعد بيانات ذات أداء فائق وسرعة قصوى في معالجة البيانات.']
                        ],
                        [
                            'title' => ['en' => 'Agile Development Methodology', 'ar' => 'منهجية التطوير الرشيقة Agile'],
                            'desc' => ['en' => 'Iterative coding with regular feedback loops, milestone reports, and deployment phases.', 'ar' => 'تطوير مرحلي منظم يتيح لك الاطلاع على التقدم خطوة بخطوة وتعديل الأولويات بمرونة.']
                        ],
                        [
                            'title' => ['en' => 'Clean & Documented Code', 'ar' => 'أكواد برمجية نظيفة وموثقة بالكامل'],
                            'desc' => ['en' => 'We follow global coding standards, ensuring easy future expansion and maintenance.', 'ar' => 'نلتزم بالمعايير القياسية العالمية في البرمجة لتسهيل التوسع والتطوير مستقبلاً.']
                        ]
                    ]
                ]
            ],
            [
                'type' => 'split_content',
                'data' => [
                    'title' => ['en' => 'Scalable Enterprise Architectures', 'ar' => 'بنية برمجية سحابية قابلة للتوسع'],
                    'content' => [
                        'en' => 'We design web applications that grow with your business. Using modular architectures, cloud-native setups, and secure APIs, we ensure your platform remains fast, reliable, and secure even during high-traffic events.',
                        'ar' => 'نصمم تطبيقات ويب تنمو مع نمو حجم أعمالك وسوقك. باستخدام بنيات برمجية مجزأة وإعدادات سحابية وواجهات ربط آمنة، نضمن بقاء منصتك سريعة وموثوقة ومحمية بالكامل حتى أثناء فترات الضغط وحركة المرور الكثيفة.'
                    ],
                    'image' => 'services/content/section-3-image.webp',
                    'image_on_right' => false,
                    'items' => [
                        ['title' => ['en' => 'Modular microservices architecture', 'ar' => 'دعم البنية البرمجية المصغرة Modular']],
                        ['title' => ['en' => 'Cloud-native deployment readiness', 'ar' => 'جاهزية كاملة للبيئات السحابية الحديثة']],
                        ['title' => ['en' => 'High security compliance standards', 'ar' => 'الالتزام بأعلى معايير الأمن والحماية الرقمية']]
                    ]
                ]
            ],
            [
                'type' => 'how_we_work_steps',
                'data' => [
                    'items' => [
                        [
                            'title' => ['en' => 'Step 1: Specifications & Scope', 'ar' => 'الخطوة الأولى: تحديد المتطلبات والمواصفات'],
                            'desc' => ['en' => 'Analyzing project goals to create a detailed system requirements specification document.', 'ar' => 'تحليل أهداف مشروعك بالتفصيل وصياغة مستند الشروط والمواصفات الفنية للبدء.']
                        ],
                        [
                            'title' => ['en' => 'Step 2: Database & System Design', 'ar' => 'الخطوة الثانية: تصميم قاعدة البيانات وهندسة النظام'],
                            'desc' => ['en' => 'Drafting entity-relationship diagrams, API structures, and software architecture.', 'ar' => 'تصميم العلاقات بين الجداول وهيكلية الواجهات البرمجية وتخطيط البنية الأساسية.']
                        ],
                        [
                            'title' => ['en' => 'Step 3: Agile Development Sprints', 'ar' => 'الخطوة الثالثة: كتابة الأكواد والبرمجة الرشيقة'],
                            'desc' => ['en' => 'Translating designs into clean backend logic and modular frontend components.', 'ar' => 'برمجة المنطق الخلفي وتطوير مكونات الواجهة الأمامية بشكل منظم وسريع.']
                        ],
                        [
                            'title' => ['en' => 'Step 4: QA, Test, & Deployment', 'ar' => 'الخطوة الرابعة: اختبار الجودة والإطلاق المباشر'],
                            'desc' => ['en' => 'Rigorous unit testing, speed testing, bug fixing, and deploying to cloud production.', 'ar' => 'إجراء اختبارات الأمان والسرعة وخلو النظام من الثغرات، ثم الإطلاق الرسمي.']
                        ]
                    ]
                ]
            ],
            [
                'type' => 'why_choose_us',
                'data' => [
                    'title' => ['en' => 'Why Develop Your Web App with Serv5?', 'ar' => 'لماذا تختار سيرف فايف لتطوير تطبيقك؟'],
                    'subtitle' => ['en' => 'We deliver high-end software engineering that secures and accelerates your business workflows.', 'ar' => 'نحن نقدم هندسة برمجيات متطورة تؤمن وتسهل دورة العمل اليومية لأعمالك.'],
                    'items' => [
                        [
                            'title' => ['en' => 'Enterprise System Engineers', 'ar' => 'مهندسو نظم بخبرة واسعة'],
                            'desc' => ['en' => 'Our team comprises senior developers who specialize in high-capacity platforms.', 'ar' => 'يضم فريقنا مبرمجين محترفين متخصصين في بناء الأنظمة والمنصات الضخمة.']
                        ],
                        [
                            'title' => ['en' => 'Strict Security-First Mindset', 'ar' => 'منهجية الأمان أولاً'],
                            'desc' => ['en' => 'We prevent vulnerabilities such as SQL injections, CSRF, and XSS by default.', 'ar' => 'نحمي أكوادنا وأنظمتنا ضد أشهر الهجمات والثغرات الأمنية تلقائياً.']
                        ],
                        [
                            'title' => ['en' => 'Third-Party Integration Experts', 'ar' => 'خبراء في الربط والدمج مع الأنظمة'],
                            'desc' => ['en' => 'Seamless integration with payment gateways, ERP tools, and shipping providers.', 'ar' => 'ربط متكامل وسلس مع بوابات الدفع الإلكتروني، أنظمة الشحن، وبرامج ERP.']
                        ],
                        [
                            'title' => ['en' => 'Robust Technical Support & Maintenance', 'ar' => 'دعم وصيانة مستمرة وموثوقة'],
                            'desc' => ['en' => 'Dedicated maintenance agreements to patch servers and update dependencies.', 'ar' => 'اتفاقيات صيانة تضمن بقاء سيرفراتك محدثة وآمنة طوال العام.']
                        ]
                    ]
                ]
            ],
            [
                'type' => 'contact_request',
                'data' => [
                    'style' => 'web_design'
                ]
            ]
        ];

        // 4. E-commerce Blocks (6 Blocks)
        $ecommerceBlocks = [
            [
                'type' => 'hero',
                'data' => [
                    'title' => ['en' => 'High-Converting E-commerce Online Stores', 'ar' => 'تصميم وبرمجة متاجر إلكترونية عالية المبيعات'],
                    'subtitle' => [
                        'en' => 'Launch your online retail store with interactive layouts, secure checkout processes, and comprehensive inventory management systems. We build stores that sell.',
                        'ar' => 'أطلق متجرك الإلكتروني بواجهات تفاعلية مذهلة، وعمليات دفع آمنة وسهلة، ونظام متكامل لإدارة المخزون والطلبات. نصنع متاجر تزيد أرباحك.'
                    ],
                    'hero_image' => 'services/heroes/web-design-hero.webp',
                ]
            ],
            [
                'type' => 'features',
                'data' => [
                    'title' => ['en' => 'Ultimate Store Features & Capabilities', 'ar' => 'أبرز ميزات المتجر الإلكتروني لدينا'],
                    'subtitle' => ['en' => 'Everything you need to showcase, manage, scale, and sell products online.', 'ar' => 'كل ما تحتاجه لعرض منتجاتك، إدارة مخزونك، بيع السلع والتسويق لمتجرك بنجاح.'],
                    'items' => [
                        [
                            'title' => ['en' => 'Advanced Shopping Cart', 'ar' => 'عربة تسوق متطورة وذكية'],
                            'desc' => ['en' => 'Frictionless checkout paths with guest purchase capabilities and saved carts.', 'ar' => 'عملية شراء سلسة تمكن العميل من الشراء كزائر أو تسجيل حساب بسهولة.']
                        ],
                        [
                            'title' => ['en' => 'Multi-Payment Integration', 'ar' => 'تكامل بوابات الدفع الإلكتروني'],
                            'desc' => ['en' => 'Seamless connection with Visa, MasterCard, Mada, PayPal, Apple Pay, and COD.', 'ar' => 'ربط متكامل مع مدى، فيزا، ماستر كارد، أبل باي، وبوابات الدفع المحلية والعالمية.']
                        ],
                        [
                            'title' => ['en' => 'Smart Inventory Control', 'ar' => 'إدارة ذكية للمخزون والمنتجات'],
                            'desc' => ['en' => 'Track stock availability, variants, and automatic low-stock alarms.', 'ar' => 'تتبع كميات المنتجات والخيارات (الألوان، المقاسات) مع تنبيهات تلقائية لنفاد الكمية.']
                        ],
                        [
                            'title' => ['en' => 'Automated Order System', 'ar' => 'نظام معالجة وتتبع الطلبات'],
                            'desc' => ['en' => 'Automatic invoice creation, shipment label export, and live customer updates.', 'ar' => 'توليد تلقائي للفواتير، تصدير بوليصات الشحن، وتحديث حالة الطلب للعميل.']
                        ],
                        [
                            'title' => ['en' => 'Mobile-Optimized Store', 'ar' => 'متجر مهيأ بالكامل للجوال'],
                            'desc' => ['en' => 'Designed with mobile screens as a priority to ensure high mobile conversion rates.', 'ar' => 'تصاميم مخصصة للهواتف تضمن تجربة تسوق سريعة ومريحة لعملاء الجوال.']
                        ],
                        [
                            'title' => ['en' => 'Multi-Vendor Support', 'ar' => 'دعم تعدد التجار والبائعين'],
                            'desc' => ['en' => 'Allow third-party vendors to sign up, list products, and manage their own orders.', 'ar' => 'السماح للتجار بالاشتراك وعرض منتجاتهم وإدارة طلباتهم بشكل مستقل بالكامل.']
                        ]
                    ]
                ]
            ],
            [
                'type' => 'split_content',
                'data' => [
                    'title' => ['en' => 'Increase Conversions with Optimized Checkout', 'ar' => 'زيادة المبيعات بفضل تجربة دفع محسنة'],
                    'content' => [
                        'en' => 'Cart abandonment is the biggest enemy of online sales. Our checkout systems are designed to minimize friction, offering guest checkouts, one-click payment options, and responsive security, leading to a 30% increase in checkout completions.',
                        'ar' => 'تخلي العملاء عن سلة الشراء هو التحدي الأكبر للتجارة الإلكترونية. تم تصميم أنظمة الدفع لدينا لتقليل خطوات الشراء، وتوفير خيارات الدفع بنقرة واحدة، وحماية أمنية متكاملة، مما يضمن زيادة بنسبة 30% في إتمام الطلبات.'
                    ],
                    'image' => 'services/content/section-2-image.webp',
                    'image_on_right' => true,
                    'items' => [
                        ['title' => ['en' => 'One-click checkout paths', 'ar' => 'خطوات شراء سريعة ومبسطة بنقرة واحدة']],
                        ['title' => ['en' => 'Secure SSL encrypted gateway connections', 'ar' => 'ربط آمن ومشفر بالكامل ببروتوكولات SSL']],
                        ['title' => ['en' => 'Smart promo code and discount rules engine', 'ar' => 'نظام ذكي وقوي لتطبيق الكوبونات والخصومات']]
                    ]
                ]
            ],
            [
                'type' => 'how_we_work_steps',
                'data' => [
                    'items' => [
                        [
                            'title' => ['en' => 'Step 1: E-commerce Strategy', 'ar' => 'الخطوة الأولى: تحديد استراتيجية المتجر والميزات'],
                            'desc' => ['en' => 'Defining target audience, payment providers, shipping networks, and product category structure.', 'ar' => 'تحديد فئات الجمهور، شركات الشحن بوابات الدفع، وتنظيم هيكلية فئات المنتجات.']
                        ],
                        [
                            'title' => ['en' => 'Step 2: Custom Shop Layouts', 'ar' => 'الخطوة الثانية: تصميم واجهات المتجر'],
                            'desc' => ['en' => 'Creating clean product pages, catalog filters, shopping cart designs, and checkout layouts.', 'ar' => 'تصميم صفحة المنتج، سلة المشتريات، صفحات الدفع، وقوائم التصفح بدقة.']
                        ],
                        [
                            'title' => ['en' => 'Step 3: Core Store Development', 'ar' => 'الخطوة الثالثة: برمجة وبربط نظام المتجر'],
                            'desc' => ['en' => 'Coding cart logic, payment gateways, shipping APIs, tax calculations, and admin panel logs.', 'ar' => 'برمجة سلة المشتريات، بوابات الدفع، حاسبة الضرائب، وربط شركات الشحن بنجاح.']
                        ],
                        [
                            'title' => ['en' => 'Step 4: Load Testing & Launch', 'ar' => 'الخطوة الرابعة: فحص السرعة والإطلاق'],
                            'desc' => ['en' => 'Testing concurrent checkouts, database response times, server load capability, and store release.', 'ar' => 'إجراء اختبارات الضغط والسرعة والأمان، وتدريبك على لوحة التحكم قبل إطلاق المتجر.']
                        ]
                    ]
                ]
            ],
            [
                'type' => 'why_choose_us',
                'data' => [
                    'title' => ['en' => 'Why Build Your E-commerce Shop with Serv5?', 'ar' => 'لماذا تختار سيرف فايف لبناء متجرك؟'],
                    'subtitle' => ['en' => 'We deploy conversion-optimized engineering to maximize your online sales and revenue.', 'ar' => 'نحن نصنع متاجر مخصصة ومبنية هندسياً لتعظيم المبيعات وتحقيق أعلى عائد استثماري.'],
                    'items' => [
                        [
                            'title' => ['en' => 'Fast Product Search & Filtering', 'ar' => 'فلاتر وبحث ذكي وفوري'],
                            'desc' => ['en' => 'Allow clients to locate items instantly by color, size, brand, or price ranges.', 'ar' => 'تسهيل عثور العميل على المنتجات باللون، المقاس، السعر أو الماركة فوراً.']
                        ],
                        [
                            'title' => ['en' => 'Lightning-Fast Page Load Times', 'ar' => 'سرعة تحميل فائقة للمتجر'],
                            'desc' => ['en' => 'Fast rendering helps boost conversions and rank higher on Google search results.', 'ar' => 'تصفح سريع يقلل ارتداد الزوار ويرفع ترتيب متجرك في نتائج بحث جوجل.']
                        ],
                        [
                            'title' => ['en' => 'Integrated Digital Marketing Tools', 'ar' => 'أدوات تسويقية متكاملة مدمجة'],
                            'desc' => ['en' => 'Built-in SEO settings, pixel tracking codes, XML sitemaps, and discount modules.', 'ar' => 'ربط مباشر بأكواد التتبع الإعلانية (سناب، فيسبوك) وتحسين سيو تلقائي.']
                        ],
                        [
                            'title' => ['en' => 'Comprehensive Analytics Reports', 'ar' => 'تقارير وتحليلات مبيعات تفصيلية'],
                            'desc' => ['en' => 'Detailed dashboard showing top products, conversion rates, and revenue statements.', 'ar' => 'لوحة تحكم توضح سلوك المشترين، المنتجات الأكثر مبيعاً، وأرباح متجرك بدقة.']
                        ]
                    ]
                ]
            ],
            [
                'type' => 'contact_request',
                'data' => [
                    'style' => 'web_design'
                ]
            ]
        ];

        // 5. Mobile Apps & App Development Blocks (7 Blocks)
        $mobileAppBlocks = [
            [
                'type' => 'hero',
                'data' => [
                    'title' => ['en' => 'Native & Cross-Platform Mobile Applications', 'ar' => 'تصميم وتطوير تطبيقات الجوال الذكية للشركات'],
                    'subtitle' => [
                        'en' => 'Deploy custom iOS and Android mobile applications developed for high scale. Engaging layouts, native fluid animations, and robust database backends.',
                        'ar' => 'أطلق تطبيقك المخصص لهواتف أندرويد وآيفون بأعلى معايير الجودة والأداء الفائق. واجهات تفاعلية جذابة، حركات سلسة، وأنظمة خلفية شديدة الاستقرار.'
                    ],
                    'hero_image' => 'services/heroes/services-hero.webp',
                ]
            ],
            [
                'type' => 'features',
                'data' => [
                    'title' => ['en' => 'State-of-the-Art Mobile Experiences', 'ar' => 'ميزات وتطبيقات جوال متطورة للمستخدمين'],
                    'subtitle' => ['en' => 'Feature-rich components built using robust, lightweight mobile frameworks.', 'ar' => 'مكونات متكاملة وغنية بالخصائص يتم بناؤها بأحدث تقنيات تطوير التطبيقات.'],
                    'items' => [
                        [
                            'title' => ['en' => 'Flutter & React Native Core', 'ar' => 'تطوير بتطبيقات فلاتر ورياكت نيتف'],
                            'desc' => ['en' => 'Cross-platform efficiency ensuring identical features and smooth designs on iOS and Android.', 'ar' => 'تطوير ذكي يضمن تشغيل التطبيق بكفاءة متطابقة وتصميم موحد على آيفون وأندرويد.']
                        ],
                        [
                            'title' => ['en' => 'High Native Performance', 'ar' => 'أداء تشغيل فائق السرعة'],
                            'desc' => ['en' => 'Optimized memory management and CPU utilization to prevent device lag or battery drain.', 'ar' => 'تحسين استخدام الذاكرة والمعالج لضمان عمل التطبيق بسلاسة تامة دون استهلاك البطارية.']
                        ],
                        [
                            'title' => ['en' => 'Push Notifications Hub', 'ar' => 'نظام إشعارات فورية وتنبيهات'],
                            'desc' => ['en' => 'Engage users with real-time alerts, offers, and transaction notifications via FCM.', 'ar' => 'تفاعل مع عملائك بإشعارات فورية حول العروض، الأخبار، وحالة الطلبات عبر السحابة.']
                        ],
                        [
                            'title' => ['en' => 'Secure User Authentication', 'ar' => 'تسجيل دخول وحماية أمنية متكاملة'],
                            'desc' => ['en' => 'Integrations for OTP SMS codes, OAuth social logins, and secure biometric scans (FaceID/TouchID).', 'ar' => 'دخول آمن عبر رقم الجوال ورمز التحقق OTP أو البصمة وحسابات التواصل الاجتماعي.']
                        ],
                        [
                            'title' => ['en' => 'Offline Database Support', 'ar' => 'دعم العمل وتخزين البيانات دون إنترنت'],
                            'desc' => ['en' => 'Store core assets locally on-device, enabling primary application functions without network connection.', 'ar' => 'تخزين البيانات الأساسية محلياً لتشغيل الوظائف الرئيسية للتطبيق عند انقطاع الاتصال.']
                        ],
                        [
                            'title' => ['en' => 'Fluid Screen Transitions', 'ar' => 'انتقالات ناعمة وحركات تفاعلية'],
                            'desc' => ['en' => 'Elegant and native-feel page gestures and view shifts that make app usage pleasant.', 'ar' => 'تأثيرات بصرية ناعمة عند التنقل بين الشاشات تمنح التطبيق طابعاً راقياً.']
                        ]
                    ]
                ]
            ],
            [
                'type' => 'portfolio',
                'data' => [
                    'section_title' => ['en' => 'Our Mobile App Gallery', 'ar' => 'معرض تطبيقات الجوال المتميزة'],
                    'is_mobile_app' => true,
                ]
            ],
            [
                'type' => 'split_content',
                'data' => [
                    'title' => ['en' => 'High-End Mobile UI/UX Design', 'ar' => 'تصاميم واجهة وتجربة مستخدم للهواتف'],
                    'content' => [
                        'en' => 'A mobile app needs to be highly intuitive. We prioritize layout convenience, clean thumb-zones, and native components, ensuring your users enjoy a seamless experience from their very first launch.',
                        'ar' => 'تطبيق الجوال يجب أن يكون بسيطاً وبديهياً للغاية. نحن نضع سهولة التصفح، وإمكانية الوصول للأزرار بالإبهام، والمكونات المدمجة للهاتف كأولوية بصرية لتوفير أفضل تجربة استخدام.'
                    ],
                    'image' => 'services/content/section-3-image.webp',
                    'image_on_right' => false,
                    'items' => [
                        ['title' => ['en' => 'Thumb-zone optimized layout arrays', 'ar' => 'توزيع أزرار ملائم لحركة اليد الطبيعية']],
                        ['title' => ['en' => 'Interactive screen swipe gestures', 'ar' => 'إيماءات تنقل تفاعلية بالسحب والتمرير']],
                        ['title' => ['en' => 'Dynamic dark mode support settings', 'ar' => 'دعم كامل للنظام الداكن التلقائي']]
                    ]
                ]
            ],
            [
                'type' => 'how_we_work_mobile',
                'data' => [
                    'items' => [
                        [
                            'title' => ['en' => 'Step 1: Concept & Wireframes', 'ar' => 'الخطوة الأولى: الفكرة وتصميم الهيكل المبدئي'],
                            'desc' => ['en' => 'Defining app layouts, flows, and mapping screen linkages using Figma.', 'ar' => 'رسم شاشات التطبيق، وتحديد مسار تصفح المستخدم وربط الشاشات هيكلياً.']
                        ],
                        [
                            'title' => ['en' => 'Step 2: Interactive Prototype', 'ar' => 'الخطوة الثانية: تصميم الواجهات التفاعلية'],
                            'desc' => ['en' => 'Visual design of all screen styles, animations, and color standards.', 'ar' => 'إضافة الألوان والخطوط والأيقونات وصناعة واجهات تفاعلية مذهلة للتطبيق.']
                        ],
                        [
                            'title' => ['en' => 'Step 3: Application Coding', 'ar' => 'الخطوة الثالثة: برمجة التطبيق وربط السيرفر'],
                            'desc' => ['en' => 'Building front-end screen states and coding API integrations with backend database.', 'ar' => 'برمجة الشاشات بالكامل وتطوير وربط الواجهات البرمجية APIs مع السيرفر.']
                        ],
                        [
                            'title' => ['en' => 'Step 4: Stores Deployment', 'ar' => 'الخطوة الرابعة: الاختبار والإطلاق في المتاجر'],
                            'desc' => ['en' => 'Testing on multiple devices and submitting application to Apple App Store and Google Play Store.', 'ar' => 'اختبار التطبيق على هواتف مختلفة ورفعه وتفعيله على متجري جوجل وآبل.']
                        ]
                    ]
                ]
            ],
            [
                'type' => 'why_choose_us',
                'data' => [
                    'title' => ['en' => 'Why Partner with Our Mobile App Team?', 'ar' => 'لماذا تطور تطبيقك مع فريق سيرف فايف؟'],
                    'subtitle' => ['en' => 'We turn complex concepts into beautiful, fluid mobile experiences that users love.', 'ar' => 'نحن نحول الأفكار والمخططات المعقدة إلى تجارب مستخدم ممتعة وسريعة على الجوال.'],
                    'items' => [
                        [
                            'title' => ['en' => 'App Store Approval Guarantee', 'ar' => 'ضمان قبول التطبيق في المتاجر'],
                            'desc' => ['en' => 'We code strictly according to Apple Guidelines and Google Policies to ensure quick store approvals.', 'ar' => 'نلتزم بالمعايير القياسية لـ Google وApple لضمان قبول التطبيق ونشره سريعاً.']
                        ],
                        [
                            'title' => ['en' => 'Clean & Documented Codebase', 'ar' => 'أكواد برمجية نظيفة ومرتبة'],
                            'desc' => ['en' => 'All modules are documented, enabling quick customization and extension in the future.', 'ar' => 'كافة أجزاء التطبيق موثقة مما يتيح لك تعديله أو إضافة ميزات جديدة مستقبلاً.']
                        ],
                        [
                            'title' => ['en' => '6 Months Free Maintenance Support', 'ar' => 'صيانة ومتابعة مجانية لمدة 6 أشهر'],
                            'desc' => ['en' => 'We resolve any potential post-launch bugs and ensure application remains stable.', 'ar' => 'نتابع معك أداء التطبيق ونحل أي مشاكل فنية قد تظهر لضمان ثباته بالكامل.']
                        ],
                        [
                            'title' => ['en' => 'Ultra-Secure API Configurations', 'ar' => 'ربط API آمن ومحمي بالكامل'],
                            'desc' => ['en' => 'All communications between app and server are tokenized and fully encrypted.', 'ar' => 'كافة الاتصالات والبيانات الصادرة والواردة بين السيرفر والتطبيق مشفرة ومؤمنة.']
                        ]
                    ]
                ]
            ],
            [
                'type' => 'contact_request',
                'data' => [
                    'style' => 'web_design'
                ]
            ]
        ];

        // 6. Digital Marketing, Social Media, Network Marketing, & SEO Blocks (6 Blocks)
        $digitalMarketingBlocks = [
            [
                'type' => 'hero',
                'data' => [
                    'title' => ['en' => 'Results-Driven Digital Marketing & SEO Services', 'ar' => 'تسويق رقمي ذكي وحملات إعلانية متكاملة'],
                    'subtitle' => [
                        'en' => 'Expand your brand footprint, drive qualified organic traffic, and maximize sales conversions with our data-backed digital marketing strategies.',
                        'ar' => 'وسع نطاق انتشار علامتك التجارية، واجذب ملايين الزوار المهتمين، وضاعف مبيعاتك وأرباحك باستراتيجيات تسويقية رقمية مبنية على الأرقام والتحليل.'
                    ],
                    'hero_image' => 'services/heroes/social-media-hero.webp',
                ]
            ],
            [
                'type' => 'digital_marketing',
                'data' => [
                    'title' => ['en' => 'Our Core Marketing Capabilities', 'ar' => 'أبرز خدماتنا التسويقية الاحترافية'],
                    'subtitle' => ['en' => 'A structured suite of digital services designed for growth.', 'ar' => 'مجموعة من الخدمات والحلول الرقمية المتكاملة المصممة لنمو مبيعاتك وحضورك.'],
                    'items' => [
                        [
                            'title' => ['en' => 'Social Media Management', 'ar' => 'إدارة حسابات التواصل الاجتماعي'],
                            'desc' => ['en' => 'Professional content maps and community management of Facebook, Instagram, Twitter, and LinkedIn.', 'ar' => 'إنشاء خطط المحتوى، التصاميم، وإدارة الحسابات على فيسبوك، انستجرام، وإكس.']
                        ],
                        [
                            'title' => ['en' => 'Search Engine Optimization (SEO)', 'ar' => 'تحسين محركات البحث SEO'],
                            'desc' => ['en' => 'Rank on the first page of Google to attract valuable organic customers daily.', 'ar' => 'تصدر نتائج البحث الأولى في جوجل لجذب عملاء مستهدفين بشكل مجاني ودائم.']
                        ],
                        [
                            'title' => ['en' => 'Paid Ads & Media Buying', 'ar' => 'إطلاق وإدارة الحملات الإعلانية الممولة'],
                            'desc' => ['en' => 'Highly targeted Google Ads and social media advertising to maximize your ROI.', 'ar' => 'إعلانات ممولة مستهدفة بدقة على جوجل، سناب، وتيك توك لتحقيق أعلى عائد.']
                        ],
                        [
                            'title' => ['en' => 'Brand Identity & Visual Content', 'ar' => 'تصميم الهوية البصرية والمحتوى المرئي'],
                            'desc' => ['en' => 'Stunning post layouts and promotional video reels to highlight your message.', 'ar' => 'تصاميم سوشيال ميديا مميزة وفيديوهات موشن تبرز قوة وجودة خدماتك ومنتجاتك.']
                        ],
                        [
                            'title' => ['en' => 'Copywriting & Content Strategy', 'ar' => 'كتابة المحتوى التسويقي والإعلاني المقنع'],
                            'desc' => ['en' => 'Persuasive sales copywriting and content maps to nurture leads and close deals.', 'ar' => 'كتابة نصوص إعلانية مقنعة وسيناريوهات فيديوهات تلامس احتياجات جمهورك.']
                        ],
                        [
                            'title' => ['en' => 'Analytics & Conversion Audits', 'ar' => 'تحليل البيانات ومعدلات التحويل'],
                            'desc' => ['en' => 'Continuous audit of conversion funnels to lower client acquisition costs.', 'ar' => 'مراجعة مستمرة لقنوات البيع وإحصائيات الحملات لتقليل تكلفة استقطاب العميل الجديد.']
                        ]
                    ]
                ]
            ],
            [
                'type' => 'split_content',
                'data' => [
                    'title' => ['en' => 'Data Analytics & Marketing Intelligence', 'ar' => 'تحليل البيانات الدقيق والذكاء التسويقي'],
                    'content' => [
                        'en' => 'We do not guess; we measure. Our marketing experts use advanced analytical tracking tools to record every click, impression, and conversion. This ensures that every penny of your marketing budget is directed towards the most profitable channels and audiences.',
                        'ar' => 'نحن لا نخمن؛ بل نقيس بالتحليل الرقمي. يستخدم خبراء التسويق لدينا أدوات تتبع تحليلية متقدمة لتسجيل كل نقرة، وظهور، وتحويل. يضمن ذلك توجيه ميزانيتك التسويقية بالكامل نحو القنوات والجماهير الأكثر ربحية وتحقيقاً للمبيعات.'
                    ],
                    'image' => 'services/content/section-3-image.webp',
                    'image_on_right' => false,
                    'items' => [
                        ['title' => ['en' => 'Dynamic Conversion Tracking setups', 'ar' => 'تتبع التحويل الديناميكي للمبيعات بدقة']],
                        ['title' => ['en' => 'Demographic Audience Insights reports', 'ar' => 'دراسة ديموغرافية دقيقة للمستهدفين والاهتمامات']],
                        ['title' => ['en' => 'Cost-Per-Acquisition Optimization models', 'ar' => 'تحسين وتقليص تكلفة العميل الجديد باستمرار']]
                    ]
                ]
            ],
            [
                'type' => 'how_we_work_timeline',
                'data' => [
                    'items' => [
                        [
                            'title' => ['en' => 'Phase 1: Research & Audit', 'ar' => 'المرحلة الأولى: دراسة السوق والتحليل الفني'],
                            'desc' => ['en' => 'Analyzing your business model, target demographic, and competitor weaknesses.', 'ar' => 'تحليل نشاطك التجاري، ودراسة السوق المستهدف، ونقاط ضعف وقوة المنافسين.']
                        ],
                        [
                            'title' => ['en' => 'Phase 2: Strategy & Planning', 'ar' => 'المرحلة الثانية: التخطيط الاستراتيجي والميزانية'],
                            'desc' => ['en' => 'Creating custom marketing funnels, keyword maps, and ad allocation schemes.', 'ar' => 'رسم خطة تسويقية مخصصة وميزانية مدروسة لتحقيق أقصى فائدة وأعلى عائد.']
                        ],
                        [
                            'title' => ['en' => 'Phase 3: Execution & Content', 'ar' => 'المرحلة الثالثة: إطلاق الحملات والمحتوى الإبداعي'],
                            'desc' => ['en' => 'Drafting copy, designing visual assets, setting up pixels, and running ads.', 'ar' => 'كتابة النصوص التسويقية، وتصميم الصور، وتهيئة تتبع أداء الحملات وإطلاقها.']
                        ],
                        [
                            'title' => ['en' => 'Phase 4: Optimization & Scaling', 'ar' => 'المرحلة الرابعة: التحسين وزيادة الميزانيات الناجحة'],
                            'desc' => ['en' => 'A/B testing ad variations, increasing budget on winning ads, and sharing reports.', 'ar' => 'اختبارات مقارنة، زيادة ميزانيات الإعلانات الناجحة وتقديم تقارير دورية شفافة.']
                        ]
                    ]
                ]
            ],
            [
                'type' => 'why_choose_us',
                'data' => [
                    'title' => ['en' => 'Why Partner with Our Marketing Team?', 'ar' => 'لماذا تختار شراكتنا التسويقية؟'],
                    'subtitle' => ['en' => 'We focus on tangible business results, sales, and revenue, not just vanity impressions.', 'ar' => 'نحن نركز على تحقيق نتائج حقيقية وملموسة وزيادة المبيعات وليس مجرد أرقام مشاهدات وهمية.'],
                    'items' => [
                        [
                            'title' => ['en' => 'ROI-Focused Campaigns', 'ar' => 'حملات تركز على العائد الاستثماري'],
                            'desc' => ['en' => 'We optimize for conversions, leads, and sales to grow your revenue.', 'ar' => 'نعمل على زيادة المبيعات وجذب العملاء المحتملين لزيادة أرباحك.']
                        ],
                        [
                            'title' => ['en' => 'Dedicated Account Managers', 'ar' => 'مدير حساب مخصص لمشروعك'],
                            'desc' => ['en' => 'A single point of contact to keep you updated on progress constantly.', 'ar' => 'شخص مسؤول يتواصل معك بشكل مستمر لإطلاعك على المستجدات والتقارير.']
                        ],
                        [
                            'title' => ['en' => 'Transparent Performance Reports', 'ar' => 'تقارير أداء دورية شفافة'],
                            'desc' => ['en' => 'Clear weekly and monthly reports detailing every aspect of the campaign.', 'ar' => 'تقارير واضحة أسبوعية وشهرية توضح أين يتم إنفاق ميزانيتك والنتائج المحققة.']
                        ],
                        [
                            'title' => ['en' => 'Multichannel Ad Mastery', 'ar' => 'إتقان الإعلانات عبر مختلف المنصات'],
                            'desc' => ['en' => 'Expertise in Google Ads, Meta Ads, TikTok, and Snapchat networks.', 'ar' => 'خبرة واسعة في إعلانات جوجل، منصات ميتا، تيك توك، وسناب شات.']
                        ]
                    ]
                ]
            ],
            [
                'type' => 'contact_request',
                'data' => [
                    'style' => 'social_media'
                ]
            ]
        ];

        // 7. Creative Design Blocks (6 Blocks)
        $creativeDesignBlocks = [
            [
                'type' => 'hero',
                'data' => [
                    'title' => ['en' => 'Brand Identity & Premium Creative Design', 'ar' => 'تصميم الهوية البصرية والتصميم الإبداعي'],
                    'subtitle' => [
                        'en' => 'Leave a memorable impression on your customers. We design unique corporate brand identities, logos, marketing materials, and high-impact social media layouts.',
                        'ar' => 'اترك انطباعاً لا ينسى لدى عملائك. نصمم هويات بصرية فريدة للشركات، شعارات مبتكرة، مواد تسويقية مطبوعة ورقيمة، وتنسيقات جذابة لحسابات التواصل الاجتماعي.'
                    ],
                    'hero_image' => 'services/heroes/services-hero.webp',
                ]
            ],
            [
                'type' => 'features',
                'data' => [
                    'title' => ['en' => 'Complete Creative Offerings', 'ar' => 'خدماتنا الإبداعية المتكاملة'],
                    'subtitle' => ['en' => 'Visual elements tailored to establish a strong brand footprint.', 'ar' => 'عناصر مرئية مصممة خصيصاً لتأسيس حضور قوي لعلامتك التجارية.'],
                    'items' => [
                        [
                            'title' => ['en' => 'Logo & Symbol Design', 'ar' => 'تصميم الشعارات والرموز الفريدة'],
                            'desc' => ['en' => 'Custom and meaningful logos that summarize your message and values.', 'ar' => 'شعارات مخصصة وذات مغزى تلخص رسالتك وقيمك التجارية.']
                        ],
                        [
                            'title' => ['en' => 'Corporate Stationery Design', 'ar' => 'تصميم المطبوعات الرسمية للشركات'],
                            'desc' => ['en' => 'Designing flyers, business cards, brochures, and banner stands.', 'ar' => 'تصميم الفلايرات، كروت العمل، البروشورات، والبنرات الدعائية.']
                        ],
                        [
                            'title' => ['en' => 'Social Media Visual Assets', 'ar' => 'تصاميم حسابات التواصل الاجتماعي'],
                            'desc' => ['en' => 'Engaging post layouts to give your social accounts a unified look.', 'ar' => 'تصاميم منشورات جذابة تمنح حساباتك الاجتماعية مظهراً متسقاً واحترافياً.']
                        ],
                        [
                            'title' => ['en' => 'Brand Style Guides', 'ar' => 'كتيب إرشادات العلامة التجارية'],
                            'desc' => ['en' => 'Rules for color usage, typography, and logo placement rules.', 'ar' => 'قواعد استخدام الألوان، الخطوط، وتنسيقات الشعار لضمان التماسك البصري.']
                        ],
                        [
                            'title' => ['en' => 'Custom Vector Illustrations', 'ar' => 'رسوم توضيحية مخصصة'],
                            'desc' => ['en' => 'Unique vector illustrations to give your website and product a premium feel.', 'ar' => 'رسومات متجهة فريدة لتمنح موقعك ومنتجاتك شعوراً مميزاً واحترافياً.']
                        ],
                        [
                            'title' => ['en' => 'Package & Label Design', 'ar' => 'تصميم العبوات والملصقات التجارية'],
                            'desc' => ['en' => 'Stunning layouts for product boxes, bottles, and commercial labels.', 'ar' => 'تصميمات مذهلة لصناديق المنتجات، والزجاجات، والملصقات التجارية.']
                        ]
                    ]
                ]
            ],
            [
                'type' => 'portfolio',
                'data' => [
                    'section_title' => ['en' => 'Our Creative Showcases', 'ar' => 'معرض تصاميمنا الإبداعية'],
                    'is_mobile_app' => false,
                ]
            ],
            [
                'type' => 'split_content',
                'data' => [
                    'title' => ['en' => 'Crafting Cohesive Visual Stories', 'ar' => 'صناعة قصة بصرية متماسكة'],
                    'content' => [
                        'en' => 'A great brand is not just a logo; it is a consistent story told through colors, shapes, and typography. Our creative designers research your values and target market to establish a cohesive brand identity that speaks directly to your customers across all physical and digital platforms.',
                        'ar' => 'العلامة التجارية الناجحة ليست مجرد شعار؛ بل هي قصة متناسقة تُروى من خلال الألوان، والأشكال، والخطوط. يدرس مصممو الإبداع لدينا قيمك وسوقك المستهدف لتأسيس هوية بصرية متماسكة تتحدث مباشرة إلى عملائك عبر جميع المنصات المادية والرقمية.'
                    ],
                    'image' => 'services/content/section-3-image.webp',
                    'image_on_right' => false,
                    'items' => [
                        ['title' => ['en' => 'Meaningful typography selection', 'ar' => 'اختيار خطوط ذات معنى ورسالة']],
                        ['title' => ['en' => 'Strategic color psychology', 'ar' => 'توظيف سيكولوجية الألوان الاستراتيجية']],
                        ['title' => ['en' => 'Vector-perfect visual scalability', 'ar' => 'رسومات متجهة دقيقة وقابلة للتكبير']]
                    ]
                ]
            ],
            [
                'type' => 'why_choose_us',
                'data' => [
                    'title' => ['en' => 'Why Choose Our Creative Studio?', 'ar' => 'لماذا تختار استوديو التصميم الإبداعي لدينا؟'],
                    'subtitle' => ['en' => 'We create artwork that communicates your values and builds deep brand loyalty.', 'ar' => 'نبتكر قطعاً فنية تتواصل مع عملائك وتبني ولاءً عميقاً للعلامة التجارية.'],
                    'items' => [
                        [
                            'title' => ['en' => '100% Original Vector Art', 'ar' => 'رسومات أصلية 100%'],
                            'desc' => ['en' => 'No stock icons or copied shapes. We craft unique visual assets.', 'ar' => 'لا نستخدم أيقونات جاهزة أو أشكالاً مقلدة. نصنع أصولاً مرئية مميزة.']
                        ],
                        [
                            'title' => ['en' => 'Detail-Oriented Execution', 'ar' => 'تنفيذ دقيق ويهتم بالتفاصيل'],
                            'desc' => ['en' => 'Every line, shade, and spacing is calculated to represent perfection.', 'ar' => 'كل خط، وظل، ومسافة مدروسة بدقة لتعكس المثالية والاحترافية.']
                        ],
                        [
                            'title' => ['en' => 'Consistency Across Channels', 'ar' => 'اتساق كامل عبر جميع المنصات'],
                            'desc' => ['en' => 'We make sure all marketing and digital channels match your guidelines.', 'ar' => 'نضمن أن جميع القنوات التسويقية والرقمية تتوافق تماماً مع دليل علامتك.']
                        ],
                        [
                            'title' => ['en' => 'Flexible Revisions Policy', 'ar' => 'سياسة مرنة للمراجعات والتعديلات'],
                            'desc' => ['en' => 'We work closely with you and refine designs until you are fully satisfied.', 'ar' => 'نعمل معك عن قرب ونقوم بتعديل التصاميم حتى تكون راضياً تماماً.']
                        ]
                    ]
                ]
            ],
            [
                'type' => 'contact_request',
                'data' => [
                    'style' => 'web_design'
                ]
            ]
        ];

        // 8. Cyber Security Blocks (6 Blocks)
        $cyberSecurityBlocks = [
            [
                'type' => 'hero',
                'data' => [
                    'title' => ['en' => 'Enterprise Cyber Security & Data Protection', 'ar' => 'الأمن السيبراني وحماية بيانات الشركات'],
                    'subtitle' => [
                        'en' => 'Protect your infrastructure, websites, and database from security threats, hacks, and malware attacks with our proactive defenses.',
                        'ar' => 'احمِ بنيتك التحتية ومواقعك وقواعد بياناتك من المخاطر الأمنية والاختراقات وهجمات البرمجيات الخبيثة بدفاعاتنا الاستباقية.'
                    ],
                    'hero_image' => 'services/heroes/hosting-hero.webp',
                ]
            ],
            [
                'type' => 'features',
                'data' => [
                    'title' => ['en' => 'Advanced Security Services', 'ar' => 'خدمات الأمن السيبراني المتقدمة'],
                    'subtitle' => ['en' => 'Comprehensive protection strategies and active defensive layers.', 'ar' => 'استراتيجيات حماية شاملة وطبقات دفاعية نشطة لتأمين بياناتك.'],
                    'items' => [
                        [
                            'title' => ['en' => 'Vulnerability Assessments', 'ar' => 'فحص وتقييم الثغرات الأمنية'],
                            'desc' => ['en' => 'Identify potential security flaws in your systems and network configuration.', 'ar' => 'تحديد نقاط الضعف والفجوات الأمنية المحتملة في أنظمتك وإعدادات شبكتك.']
                        ],
                        [
                            'title' => ['en' => 'Penetration Testing', 'ar' => 'اختبارات الاختراق والجهوزية'],
                            'desc' => ['en' => 'Simulate malicious attacks to assess the defense capabilities of your platform.', 'ar' => 'محاكاة الهجمات والتهديدات الخارجية لتقييم كفاءة وقوة دفاعات أنظمتك.']
                        ],
                        [
                            'title' => ['en' => 'Intrusion Detection Systems', 'ar' => 'أنظمة الكشف عن التسلل والتهديدات'],
                            'desc' => ['en' => 'Active monitoring systems to discover and log unauthorized access attempts.', 'ar' => 'أنظمة مراقبة حية لاكتشاف وتسجيل أي محاولات وصول غير مصرح بها فوراً.']
                        ],
                        [
                            'title' => ['en' => 'Data Encryption Setup', 'ar' => 'تشفير وحماية البيانات الحساسة'],
                            'desc' => ['en' => 'Implementing industry-grade database and transit encryption technologies.', 'ar' => 'تطبيق تشفير قوي لقواعد البيانات والبيانات المتنقلة لحمايتها من التنصت.']
                        ],
                        [
                            'title' => ['en' => 'Malware Scan & Removal', 'ar' => 'فحص وإزالة البرمجيات الخبيثة'],
                            'desc' => ['en' => 'Regular server sanitization and real-time trojan scans to prevent system hijacking.', 'ar' => 'فحص دوري للخوادم وتطهيرها من ملفات التجسس والفيروسات لحماية السيرفر.']
                        ],
                        [
                            'title' => ['en' => 'Regulatory Compliance Audits', 'ar' => 'تدقيق التوافق مع المعايير الأمنية'],
                            'desc' => ['en' => 'Verify compatibility with global compliance models like GDPR or local cybersecurity standards.', 'ar' => 'التأكد من التوافق الكامل مع قوانين حماية البيانات المحلية والعالمية.']
                        ]
                    ]
                ]
            ],
            [
                'type' => 'split_content',
                'data' => [
                    'title' => ['en' => 'Proactive Shield Against Cyber Threats', 'ar' => 'درع استباقي ضد التهديدات السيبرانية'],
                    'content' => [
                        'en' => 'Security is not a single tool; it is a continuous posture. Our engineers monitor your systems, deploy automated vulnerability scanners, and perform simulation attacks to find and patch security gaps before hackers can exploit them.',
                        'ar' => 'الحماية ليست مجرد أداة واحدة، بل هي حالة دفاع مستمر. يقوم مهندسو الحماية لدينا بمراقبة أنظمتك، وتفعيل ماسحات الثغرات التلقائية، وإجراء محاكاة للاختراقات لاكتشاف وسد الفجوات الأمنية قبل استغلالها من المخترقين.'
                    ],
                    'image' => 'services/content/section-2-image.webp',
                    'image_on_right' => true,
                    'items' => [
                        ['title' => ['en' => 'Active server security monitoring', 'ar' => 'مراقبة حية وأمنية مستمرة للخوادم']],
                        ['title' => ['en' => 'Automated firewall rule updates', 'ar' => 'تحديثات تلقائية لقواعد جدران الحماية']],
                        ['title' => ['en' => 'Immediate threat mitigation rules', 'ar' => 'إجراءات استجابة سريعة للحد من المخاطر']]
                    ]
                ]
            ],
            [
                'type' => 'how_we_work_steps',
                'data' => [
                    'items' => [
                        [
                            'title' => ['en' => 'Step 1: Security Audit', 'ar' => 'الخطوة الأولى: تدقيق وفحص الثغرات الحالية'],
                            'desc' => ['en' => 'Complete investigation of your servers, networks, APIs, and access configurations.', 'ar' => 'فحص شامل للخوادم، الشبكات، والواجهات البرمجية وإعدادات الصلاحيات.']
                        ],
                        [
                            'title' => ['en' => 'Step 2: System Hardening', 'ar' => 'الخطوة الثانية: تقوية وتأمين الأنظمة'],
                            'desc' => ['en' => 'Applying security configurations, updating dependencies, and shutting down unused ports.', 'ar' => 'تثبيت الإعدادات الأمنية، وتحديث البرمجيات وإغلاق المنافذ غير المستخدمة.']
                        ],
                        [
                            'title' => ['en' => 'Step 3: Penetration Testing', 'ar' => 'الخطوة الثالثة: اختبارات اختراق بيضاء'],
                            'desc' => ['en' => 'Simulating custom cyberattacks to evaluate the newly applied security setups.', 'ar' => 'إجراء هجمات تجريبية بيضاء لتقييم كفاءة الحماية والتأمين الجديد.']
                        ],
                        [
                            'title' => ['en' => 'Step 4: Compliance & Handover', 'ar' => 'الخطوة الرابعة: التقرير والشهادة الأمنية'],
                            'desc' => ['en' => 'Handing over the security logs, remediation guides, and system status.', 'ar' => 'تسليم ملفات الفحص، وتقارير المعالجة، وشهادة تأمين النظام والجهوزية.']
                        ]
                    ]
                ]
            ],
            [
                'type' => 'why_choose_us',
                'data' => [
                    'title' => ['en' => 'Why Trust Serv5 with Your Security?', 'ar' => 'لماذا تثق بـ سيرف فايف لحماية أعمالك؟'],
                    'subtitle' => ['en' => 'We follow global standards to secure critical digital assets and protect your user data.', 'ar' => 'نتبع المعايير العالمية لتأمين الأصول الرقمية الحيوية وحماية بيانات المستخدمين.'],
                    'items' => [
                        [
                            'title' => ['en' => 'Certified Security Experts', 'ar' => 'خبراء أمن سيبراني معتمدون'],
                            'desc' => ['en' => 'Our team includes security specialists with global certifications (CEH, CISSP).', 'ar' => 'يضم فريقنا خبراء أمنيين يحملون شهادات مهنية دولية معتمدة.']
                        ],
                        [
                            'title' => ['en' => '24/7 Monitoring & Auditing', 'ar' => 'مراقبة وتدقيق على مدار الساعة'],
                            'desc' => ['en' => 'Active alarm setups to notify and handle potential threats instantly.', 'ar' => 'إعدادات إنذار ذكية للتبليغ الفوري عن أي سلوك مريب داخل الأنظمة.']
                        ],
                        [
                            'title' => ['en' => 'Confidentiality Guaranteed', 'ar' => 'سرية وخصوصية كاملة للبيانات'],
                            'desc' => ['en' => 'Strict NDAs protect your system assets and private operations information.', 'ar' => 'اتفاقيات سرية صارمة لحماية خصوصية بياناتك وتفاصيل البنية التحتية.']
                        ],
                        [
                            'title' => ['en' => 'Zero Data-Loss Recovery Plans', 'ar' => 'خطط استعادة البيانات دون فقدانها'],
                            'desc' => ['en' => 'Automated disaster recovery solutions to restore operations within minutes.', 'ar' => 'حلول استعادة كوارث متطورة لإعادة تشغيل النظام فوراً بعد أي طارئ.']
                        ]
                    ]
                ]
            ],
            [
                'type' => 'contact_request',
                'data' => [
                    'style' => 'web_design'
                ]
            ]
        ];

        // 9. Technical Support Blocks (6 Blocks)
        $techSupportBlocks = [
            [
                'type' => 'hero',
                'data' => [
                    'title' => ['en' => '24/7 Professional Technical Support & Maintenance', 'ar' => 'الدعم الفني والصيانة الاحترافية على مدار الساعة'],
                    'subtitle' => [
                        'en' => 'Keep your digital systems running smoothly. We offer server monitoring, bug fixing, updates, and optimization at any hour.',
                        'ar' => 'حافظ على تشغيل أنظمتك الرقمية بسلاسة تامة. نقدم مراقبة الخوادم، إصلاح الأخطاء، التحديثات الدورية، وتحسين الأداء في أي وقت.'
                    ],
                    'hero_image' => 'services/heroes/hosting-hero.webp',
                ]
            ],
            [
                'type' => 'features',
                'data' => [
                    'title' => ['en' => 'Full-Scope System Maintenance Capabilities', 'ar' => 'صيانة شاملة للموقع والأنظمة الإلكترونية'],
                    'subtitle' => ['en' => 'Proactive checks and fast resolutions to keep your business online without interruption.', 'ar' => 'فحوصات استباقية وحلول سريعة للحفاظ على نشاطك التجاري متصلاً بالإنترنت دون انقطاع.'],
                    'items' => [
                        [
                            'title' => ['en' => 'Server Setup & Health Monitoring', 'ar' => 'إعداد السيرفر ومراقبة سلامته'],
                            'desc' => ['en' => 'Configuring hosting nodes and monitoring memory, disk, and load capacities.', 'ar' => 'تهيئة الخوادم وتتبع استهلاك الذاكرة ومساحة القرص لضمان الاستقرار.']
                        ],
                        [
                            'title' => ['en' => 'Bug Fixing & Code Auditing', 'ar' => 'إصلاح الأخطاء وفحص الأكواد'],
                            'desc' => ['en' => 'Troubleshooting functional application bugs and resolving exceptions in code.', 'ar' => 'اكتشاف وحل المشكلات الفنية وأخطاء الأكواد في البرمجيات المختلفة.']
                        ],
                        [
                            'title' => ['en' => 'Speed & Performance Optimization', 'ar' => 'تحسين سرعة وأداء الأنظمة والمواقع'],
                            'desc' => ['en' => 'Optimizing database queries, caching layers, and minifying asset bundles.', 'ar' => 'تحسين استعلامات قواعد البيانات والتخزين المؤقت وضغط الصور لتسريع التصفح.']
                        ],
                        [
                            'title' => ['en' => 'Automated Backup Management', 'ar' => 'إدارة النسخ الاحتياطي التلقائي'],
                            'desc' => ['en' => 'Configuring secure daily backup routines to remote cloud directories.', 'ar' => 'إعداد خطط نسخ احتياطي يومي وأسبوعي تلقائي للملفات في مواقع تخزين سحابية آمنة.']
                        ],
                        [
                            'title' => ['en' => 'SSL & Security Patching', 'ar' => 'تثبيت شهادات SSL والتحديثات الأمنية'],
                            'desc' => ['en' => 'Applying framework updates and security updates to block potential server entries.', 'ar' => 'تحديث مكتبات البرمجة وسد الثغرات الأمنية للحفاظ على استمرارية الأمان.']
                        ],
                        [
                            'title' => ['en' => 'Domain & Email Administration', 'ar' => 'إدارة النطاقات ورسائل البريد الإلكتروني'],
                            'desc' => ['en' => 'Managing MX records, SPF configurations, DKIM setups, and resolve email delivery.', 'ar' => 'تهيئة النطاقات وإعدادات MX وSPF وDKIM لضمان وصول رسائل البريد الإلكتروني.']
                        ]
                    ]
                ]
            ],
            [
                'type' => 'split_content',
                'data' => [
                    'title' => ['en' => 'Continuous Maintenance Guarantee', 'ar' => 'ضمان الصيانة المستمرة والوقائية'],
                    'content' => [
                        'en' => 'We believe in proactive maintenance rather than reactive patching. Our support team conducts routine database audits, cleans temporary system logs, checks server load capacities, and ensures all security patches are applied, keeping your downtime at absolute zero.',
                        'ar' => 'نحن نؤمن بالصيانة الاستباقية بدلاً من انتظار حدوث العطل. يجري فريق الدعم لدينا تدقيقاً دورياً لقواعد البيانات، وينظف سجلات النظام المؤقتة، ويفحص سعة استيعاب الخادم، ويضمن تثبيت كافة التحديثات الأمنية لتفادي التوقف.'
                    ],
                    'image' => 'services/content/section-3-image.webp',
                    'image_on_right' => false,
                    'items' => [
                        ['title' => ['en' => 'Routine weekly database audits', 'ar' => 'تدقيق دوري أسبوعي لقواعد البيانات']],
                        ['title' => ['en' => 'Live server load and traffic analysis', 'ar' => 'تحليل فوري ومستمر لضغط وحركة الخادم']],
                        ['title' => ['en' => 'Emergency ticket response priority', 'ar' => 'أولوية قصوى لتذاكر الطوارئ والأعطال المفاجئة']]
                    ]
                ]
            ],
            [
                'type' => 'how_we_work_steps',
                'data' => [
                    'items' => [
                        [
                            'title' => ['en' => 'Step 1: Diagnostics', 'ar' => 'الخطوة الأولى: الفحص والتشخيص الفني للأنظمة'],
                            'desc' => ['en' => 'Complete investigation of your code status, logs, and current server setup.', 'ar' => 'مراجعة شاملة لملفات السجل، وأكواد التطبيقات، وإعدادات السيرفر الحالية.']
                        ],
                        [
                            'title' => ['en' => 'Step 2: Security & Optimization', 'ar' => 'الخطوة الثانية: التأمين وتحسين الأداء والسرعة'],
                            'desc' => ['en' => 'Patching immediate vulnerabilities, cleaning databases, and configuring server cache.', 'ar' => 'معالجة الثغرات العاجلة، وتنظيف قواعد البيانات، وضبط كاش السيرفر.']
                        ],
                        [
                            'title' => ['en' => 'Step 3: Setup Monitoring Alarms', 'ar' => 'الخطوة الثالثة: تفعيل إنذارات المراقبة الفورية'],
                            'desc' => ['en' => 'Setting up active tools to alert on downtime, memory spikes, or security threats.', 'ar' => 'ربط وتفعيل أدوات مراقبة تنبهنا تلقائياً عند توقف الموقع أو زيادة الحمل.']
                        ],
                        [
                            'title' => ['en' => 'Step 4: Maintenance Logs', 'ar' => 'الخطوة الرابعة: التقارير الدورية وسجلات العمل'],
                            'desc' => ['en' => 'Sharing structured monthly reports detailing system health status and applied changes.', 'ar' => 'مشاركة تقارير شهرية توضح حالة النظام الفنية وما تم إنجازه وصيانته.']
                        ]
                    ]
                ]
            ],
            [
                'type' => 'why_choose_us',
                'data' => [
                    'title' => ['en' => 'Why Partner with Our Support Team?', 'ar' => 'لماذا تختار فريق الدعم الفني لدينا؟'],
                    'subtitle' => ['en' => 'Technical excellence and immediate solutions are just a support ticket away.', 'ar' => 'التميز التقني والحل الفوري لكافة مشاكلك البرمجية على بعد تذكرة دعم واحدة.'],
                    'items' => [
                        [
                            'title' => ['en' => 'Guaranteed Response SLAs', 'ar' => 'ضمان وقت استجابة محدد SLA'],
                            'desc' => ['en' => 'We commit to responding within critical timelines for emergency server errors.', 'ar' => 'نلتزم بالاستجابة في غضون دقائق لحالات تعطل الخادم أو توقف الخدمة.']
                        ],
                        [
                            'title' => ['en' => 'Multi-Channel Support Desk', 'ar' => 'مكتب دعم فني متعدد القنوات'],
                            'desc' => ['en' => 'Create tickets, start a live WhatsApp chat, or call our customer service hotline.', 'ar' => 'افتح تذكرة دعم، تواصل معنا مباشرة عبر واتساب، أو اتصل بنا هاتفياً.']
                        ],
                        [
                            'title' => ['en' => 'Dedicated System Admins', 'ar' => 'مهندسو خوادم ومديرو أنظمة مخصصون'],
                            'desc' => ['en' => 'Your systems will be handled by qualified administrators with years of experience.', 'ar' => 'يتولى رعاية أنظمتك وتأمينها مهندسو نظم مؤهلون ومحترفون.']
                        ],
                        [
                            'title' => ['en' => 'Detailed System Reports', 'ar' => 'تقارير فنية دورية تفصيلية'],
                            'desc' => ['en' => 'We provide comprehensive summaries of downtime, security events, and database usage.', 'ar' => 'نوفر لك كشوفات دورية واضحة حول استقرار الخادم والأمان ومعدلات الاستخدام.']
                        ]
                    ]
                ]
            ],
            [
                'type' => 'contact_request',
                'data' => [
                    'style' => 'web_design'
                ]
            ]
        ];

        // Slugs mapping to services config
        $mapping = [
            'web-hosting' => [
                'name' => ['en' => 'Web Hosting', 'ar' => 'استضافة المواقع'],
                'description' => [
                    'en' => 'Get the most powerful, secure, and fast website hosting with high-performance storage and DDoS shield protection.',
                    'ar' => 'معنا، ستحصل على أقوى استضافة مواقع توفر لك ميزات فائقة الأداء وتساعدك في الحفاظ على أمن موقعك واستقراره.'
                ],
                'icon' => '/Services/Purple Planet.webp',
                'template' => 'template-three',
                'blocks' => $webHostingBlocks,
            ],
            'cloud-hosting' => [
                'name' => ['en' => 'Cloud Hosting', 'ar' => 'الاستضافة السحابية'],
                'description' => [
                    'en' => 'Deploy your business websites on fully managed cloud infrastructure with scalable resources and automated backups.',
                    'ar' => 'انشر مواقع أعمالك على بنية تحتية سحابية مخصصة وسريعة تضمن استمرارية التشغيل الكاملة.'
                ],
                'icon' => '/Services/Purple Planet.webp',
                'template' => 'template-three',
                'blocks' => $webHostingBlocks,
            ],
            'web-design' => [
                'name' => ['en' => 'Web Design', 'ar' => 'تصميم المواقع'],
                'description' => [
                    'en' => 'Create stunning, fully responsive, and conversion-optimized web designs that capture your corporate brand identity.',
                    'ar' => 'أنشئ مواقع ويب مذهلة وسريعة الاستجابة وسهلة الاستخدام تأسر جمهورك وتمثل علامتك التجارية بشكل مثالي.'
                ],
                'icon' => '/Services/Yellow Planet.svg',
                'template' => 'template-two',
                'blocks' => $webDesignBlocks,
            ],
            'ui-ux-design' => [
                'name' => ['en' => 'UI/UX Design', 'ar' => 'تصميم واجهة وتجربة المستخدم'],
                'description' => [
                    'en' => 'High-fidelity wireframes and interactive user interfaces designed with user convenience and modern aesthetics.',
                    'ar' => 'نصمم واجهات وتجارب مستخدم متكاملة ومدروسة تسهل على الزوار الوصول لأهدافهم وتزيد مبيعاتك.'
                ],
                'icon' => '/Services/Yellow Planet.svg',
                'template' => 'template-two',
                'blocks' => $webDesignBlocks,
            ],
            'digital-identity' => [
                'name' => ['en' => 'Digital Identity & Branding', 'ar' => 'الهوية الرقمية والعلامة التجارية'],
                'description' => [
                    'en' => 'Complete digital branding solutions including custom logo design, color guidelines, typography, and marketing assets.',
                    'ar' => 'حلول متكاملة لبناء هويتك الرقمية تشمل تصميم الشعارات، الكتيبات الإرشادية للألوان والخطوط.'
                ],
                'icon' => '/Services/Yellow Planet.svg',
                'template' => 'template-two',
                'blocks' => $webDesignBlocks,
            ],
            'web-development' => [
                'name' => ['en' => 'Web Development', 'ar' => 'تطوير المواقع'],
                'description' => [
                    'en' => 'Custom full-stack web applications coded using React, Next.js, and Laravel, optimized for speed and scaling.',
                    'ar' => 'نطور تطبيقات ومواقع ويب مخصصة قوية وسريعة باستخدام أحدث تقنيات الويب لحلول برمجية متكاملة.'
                ],
                'icon' => '/Services/Blue Planet.webp',
                'template' => 'template-one',
                'blocks' => $webDevBlocks,
            ],
            'ecommerce' => [
                'name' => ['en' => 'E-commerce Solutions', 'ar' => 'حلول المتاجر الإلكترونية'],
                'description' => [
                    'en' => 'Feature-rich online store design with shopping cart, multi-payment options, automated order routing, and inventory sync.',
                    'ar' => 'أطلق متجرك الإلكتروني المجهز لعرض وبيع منتجاتك وقبول المدفوعات والربط مع شركات الشحن المختلفة.'
                ],
                'icon' => '/Services/Yellow Planet.svg',
                'template' => 'template-two',
                'blocks' => $ecommerceBlocks,
            ],
            'mobile-apps' => [
                'name' => ['en' => 'Mobile Applications', 'ar' => 'تطبيقات الجوال'],
                'description' => [
                    'en' => 'High-capacity iOS and Android app development using Flutter and React Native, with secure APIs and notifications.',
                    'ar' => 'تطبيقات متميزة وسلسة لأجهزة آيفون وأندرويد بأحدث معايير الأمان والتوافق والربط السحابي.'
                ],
                'icon' => '/Services/new/green.webp',
                'template' => 'template-two',
                'blocks' => $mobileAppBlocks,
            ],
            'app-development' => [
                'name' => ['en' => 'Mobile App Development', 'ar' => 'تطوير تطبيقات الجوال'],
                'description' => [
                    'en' => 'We code robust native iOS and Android apps, managing the development lifecycle from UI design to app store submission.',
                    'ar' => 'نقوم ببرمجة وتطوير تطبيقات الهواتف الذكية وتجهيزها ورفعها بالكامل على متجري آبل بلاي وجوجل بلاي.'
                ],
                'icon' => '/Services/new/green.webp',
                'template' => 'template-two',
                'blocks' => $mobileAppBlocks,
            ],
            'digital-marketing' => [
                'name' => ['en' => 'Digital Marketing', 'ar' => 'التسويق الرقمي'],
                'description' => [
                    'en' => 'Increase your market reach, attract valuable organic leads, and scale your brand visibility with modern ad strategies.',
                    'ar' => 'وسع نطاق وصول علامتك التجارية، وابنِ الثقة، وحقق المبيعات باستراتيجياتنا التسويقية المتكاملة.'
                ],
                'icon' => '/Services/Blue Planet.webp',
                'template' => 'template-one',
                'blocks' => $digitalMarketingBlocks,
            ],
            'social-media' => [
                'name' => ['en' => 'Social Media Marketing', 'ar' => 'التسويق عبر السوشيال ميديا'],
                'description' => [
                    'en' => 'Expert management of social media accounts with regular updates, graphic layouts, content plans, and paid ad funnels.',
                    'ar' => 'إدارة احترافية لحسابات التواصل الاجتماعي تشمل تصميم المنشورات وإعداد خطط المحتوى والحملات الإعلانية.'
                ],
                'icon' => '/Services/Blue Planet.webp',
                'template' => 'template-one',
                'blocks' => $digitalMarketingBlocks,
            ],
            'network-marketing' => [
                'name' => ['en' => 'Network Marketing', 'ar' => 'التسويق الشبكي'],
                'description' => [
                    'en' => 'Strategic affiliate networks and marketing setups to scale customer acquisition channels and generate recurring revenue.',
                    'ar' => 'استراتيجيات وخطط التسويق بالعمولة والشبكات لزيادة قنوات استقطاب العملاء وبناء دخل متكرر.'
                ],
                'icon' => '/Services/Blue Planet.webp',
                'template' => 'template-one',
                'blocks' => $digitalMarketingBlocks,
            ],
            'seo-optimization' => [
                'name' => ['en' => 'SEO Search Optimization', 'ar' => 'تحسين محركات البحث SEO'],
                'description' => [
                    'en' => 'On-page audits, technical SEO fixes, high-value keyword indexing, and backlink strategies to rank first on search engines.',
                    'ar' => 'تهيئة الموقع الفنية وتوفير الكلمات المفتاحية وبناء الروابط للحصول على ترتيب متقدم في جوجل.'
                ],
                'icon' => '/Services/Blue Planet.webp',
                'template' => 'template-one',
                'blocks' => $digitalMarketingBlocks,
            ],
            'creative-design' => [
                'name' => ['en' => 'Creative Design', 'ar' => 'التصميم الإبداعي'],
                'description' => [
                    'en' => 'Craft premium logos, commercial packaging, marketing illustrations, and corporate banners that make a lasting impression.',
                    'ar' => 'اجعل أفكارك تنبض بالحياة من خلال حلول التصميم الإبداعي لدينا التي تبرز وتترك انطباعاً دائماً.'
                ],
                'icon' => '/Services/new/green.webp',
                'template' => 'template-two',
                'blocks' => $creativeDesignBlocks,
            ],
            'cyber-security' => [
                'name' => ['en' => 'Cyber Security', 'ar' => 'الأمن السيبراني'],
                'description' => [
                    'en' => 'Enterprise-grade system protection, active network firewalls, vulnerability scanning, and secure compliance certifications.',
                    'ar' => 'تأمين كامل للبنية التحتية والمواقع الإلكترونية وقواعد البيانات ضد محاولات الاختراق والهجمات السيبرانية.'
                ],
                'icon' => '/Services/Purple Planet.webp',
                'template' => 'template-three',
                'blocks' => $cyberSecurityBlocks,
            ],
            'aldaam-alfny' => [
                'name' => ['en' => 'Technical Support', 'ar' => 'الدعم الفني والصيانة'],
                'description' => [
                    'en' => '24/7 technical monitoring, database optimization, daily health backups, and immediate bug resolution agreements.',
                    'ar' => 'دعم فني وصيانة متواصلة لحل أي مشكلات فنية أو برمجية في الخوادم أو المواقع على مدار الساعة.'
                ],
                'icon' => '/Services/Purple Planet.webp',
                'template' => 'template-three',
                'blocks' => $techSupportBlocks,
            ],
        ];

        foreach ($mapping as $slug => $data) {
            $sections = [
                'template' => $data['template'],
                'blocks' => $data['blocks']
            ];

            Service::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $data['name'],
                    'description' => $data['description'],
                    'icon' => $data['icon'],
                    'is_active' => true,
                    'sections' => $sections
                ]
            );
        }
    }
}
