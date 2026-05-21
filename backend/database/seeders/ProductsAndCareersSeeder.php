<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Career;

class ProductsAndCareersSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Products
        $productsData = [
            [
                'slug' => 'athath',
                'name' => ['en' => 'Athath', 'ar' => 'أثاث'],
                'category' => ['en' => 'Furniture Store', 'ar' => 'تجارة إلكترونية للأثاث'],
                'title' => ['en' => 'Athath website design', 'ar' => 'منصة أثاث المتكاملة للتجارة الإلكترونية'],
                'description' => [
                    'en' => 'Athath is a complete e-commerce solution for furniture stores. It offers a modern, responsive design with advanced product filtering, seamless checkout, and an elegant presentation of your furniture collections. Perfect for establishing a strong online presence.',
                    'ar' => 'منصة "أثاث" هي حل تجارة إلكترونية راقٍ ومصمم خصيصاً لمعارض الأثاث واستوديوهات التصميم الداخلي. تجمع المنصة بين جماليات التصميم العصري والأداء البرمجي الفائق، لتوفر للعملاء تجربة تسوق تفاعلية سلسة تحول الزوار إلى عملاء دائمين.'
                ],
                'image' => '/portfolio/image-1.png',
                'demo_link' => '#',
                'order_link' => '#',
                'features' => [
                    'en' => [
                        ['title' => 'Dynamic High-Res Gallery', 'desc' => 'Showcase furniture textures and details with high-resolution responsive zooming.'],
                        ['title' => 'Smart Advanced Filtering', 'desc' => 'Enable customers to instantly filter by collection, material, color, and price.'],
                        ['title' => 'Seamless Glassmorphic Cart', 'desc' => 'A frictionless one-page checkout designed to reduce cart abandonment rates.'],
                        ['title' => 'Intelligent Inventory Control', 'desc' => 'Real-time stock management and order notifications for business owners.']
                    ],
                    'ar' => [
                        ['title' => 'معرض صور تفاعلي دقيق', 'desc' => 'استعرض تفاصيل وخامات الأثاث بدقة متناهية مع خاصية التكبير والتفاعل الفائق.'],
                        ['title' => 'نظام فلترة ذكي ومتقدم', 'desc' => 'يتيح للعملاء العثور على قطع الأثاث المطلوبة فوراً عبر تصفية الخامات، الألوان، والأسعار.'],
                        ['title' => 'سلة دفع زجاجية ذكية', 'desc' => 'عملية دفع من صفحة واحدة خالية من التعقيد لزيادة نسبة إتمام المبيعات وتقليل مغادرة السلة.'],
                        ['title' => 'لوحة تحكم ذكية للمخزون', 'desc' => 'إدارة المخزون والطلبات فورياً وتلقي تنبيهات المبيعات أولاً بأول لأصحاب الأعمال.']
                    ]
                ],
                'tech_specs' => ['Next.js', 'React', 'TailwindCSS', 'Node.js', 'MongoDB', 'Framer Motion'],
                'why_choose_us' => [
                    'en' => 'Ready-to-launch furniture store with ultra-fast page load speeds, pre-configured checkout, and SEO-optimized structure.',
                    'ar' => 'متجر أثاث متكامل جاهز للإطلاق الفوري، يتميز بسرعات تحميل فائقة، ونظام دفع مهيأ، وبنية برمجية مهيأة بالكامل لمحركات البحث (SEO).'
                ],
                'samples' => ["/portfolio/image-1.png", "/portfolio/image-2.png", "/portfolio/image-3.png", "/portfolio/image-4.png"]
            ],
            [
                'slug' => 'clinic',
                'name' => ['en' => 'Clinic+', 'ar' => 'كلينيك بلس'],
                'category' => ['en' => 'Medical Clinic', 'ar' => 'إدارة الرعاية الصحية'],
                'title' => ['en' => 'Clinic+ Medical System', 'ar' => 'نظام كلينيك بلس الطبي المتكامل'],
                'description' => [
                    'en' => 'A comprehensive medical clinic management system. Allows patients to book appointments, view their medical history, and interact with doctors. It includes a complete dashboard for doctors and receptionists.',
                    'ar' => 'نظام "كلينيك بلس" هو منصة متطورة لإدارة العيادات والمراكز الطبية بكافة تخصصاتها. تم تطويره بهدف تسهيل حجز مواعيد المرضى، وتنظيم الملفات الطبية الرقمية، وأتمتة المهام اليومية للأطباء والموظفين بكفاءة وسرية مطلقة.'
                ],
                'image' => '/portfolio/image-2.png',
                'demo_link' => '#',
                'order_link' => '#',
                'features' => [
                    'en' => [
                        ['title' => 'Real-Time Scheduling', 'desc' => 'Avoid overlapping and booking conflicts with a dynamic smart medical calendar.'],
                        ['title' => 'Secure Health Records (EHR)', 'desc' => 'Confidential digital patient histories, active prescriptions, and diagnosis logs.'],
                        ['title' => 'Doctor & Receptionist Hub', 'desc' => 'Custom role-based logins with unique views to manage clinic operations.'],
                        ['title' => 'Automated Smart Reminders', 'desc' => 'Reduce patient no-shows with scheduled, automated notifications.']
                    ],
                    'ar' => [
                        ['title' => 'جدولة مواعيد ذكية وحية', 'desc' => 'حجز مواعيد متزامن بدون تعارض مع توفير جدول تقويمي ذكي للأطباء والمرضى.'],
                        ['title' => 'ملفات صحية رقمية آمنة', 'desc' => 'حفظ مشفر لتاريخ المرضى الطبي، الروشتات العلاجية، وتقارير التشخيص.'],
                        ['title' => 'بوابات مخصصة للكادر الطبي', 'desc' => 'لوحات تحكم بأدوار محددة وصلاحيات أمان صارمة لإدارة العيادة اليومية.'],
                        ['title' => 'تنبيهات وتذكيرات تلقائية', 'desc' => 'تقليل غياب المرضى عبر إرسال تذكيرات آلية عبر الرسائل القصيرة والبريد الإلكتروني.']
                    ]
                ],
                'tech_specs' => ['React', 'Next.js', 'TailwindCSS', 'Express.js', 'PostgreSQL', 'Node.js'],
                'why_choose_us' => [
                    'en' => 'HIPAA-compliant data handling structures, ultra-smooth clinic workflow management, and zero setup latency.',
                    'ar' => 'تصميم يتوافق مع معايير الأمان الطبية، إدارة مرنة للورديات، وتجربة مستخدم خالية من التعقيد للمرضى والأطباء.'
                ],
                'samples' => ["/portfolio/image-2.png", "/portfolio/image-3.png", "/portfolio/image-4.png", "/portfolio/image-5.png"]
            ],
            [
                'slug' => 'edu-platform',
                'name' => ['en' => 'EduLearn', 'ar' => 'إديوليرن'],
                'category' => ['en' => 'E-Learning', 'ar' => 'المنصات التعليمية وأنظمة التعلم'],
                'title' => ['en' => 'EduLearn Educational Platform', 'ar' => 'منصة إديوليرن التعليمية والتدريبية'],
                'description' => [
                    'en' => 'A complete online learning management system. Instructors can upload courses, track student progress, and conduct live sessions. Students enjoy an interactive learning environment with quizzes and certificates.',
                    'ar' => 'منصة "إديوليرن" هي نظام إدارة تعلم (LMS) شامل ومبتكر لخدمة المدارس، الأكاديميات، والمحاضرين الخصوصيين. تتيح لك المنصة إنشاء، واستضافة، وبيع الكورسات التفاعلية بجودة ممتازة وبيئة تعليمية تواكب أحدث المعايير.'
                ],
                'image' => '/portfolio/image-3.png',
                'demo_link' => '#',
                'order_link' => '#',
                'features' => [
                    'en' => [
                        ['title' => 'Structured Course Builder', 'desc' => 'Organize lessons by chapters, upload video files, PDFs, and rich-text worksheets.'],
                        ['title' => 'Interactive Quiz Engine', 'desc' => 'Test student knowledge with auto-graded multiple-choice, essay, and time-bound quizzes.'],
                        ['title' => 'Progress & Analytics Tracker', 'desc' => 'Give students and parents real-time insights into course completion statistics.'],
                        ['title' => 'Smart Certificate Generator', 'desc' => 'Instantly reward graduating students with custom, dynamically-generated PDF credentials.']
                    ],
                    'ar' => [
                        ['title' => 'منشئ كورسات منظم وهيكلي', 'desc' => 'تنظيم الدروس في فصول، ورفع مقاطع الفيديو، وملفات PDF، وأوراق العمل التفاعلية.'],
                        ['title' => 'نظام اختبارات متطور', 'desc' => 'تقييم معرفة الطلاب باختبارات مؤقتة وتصحيح تلقائي فوري متعدد الخيارات.'],
                        ['title' => 'تتبع التقدم والتحليلات للمتعلم', 'desc' => 'إحصائيات فورية وتفصيلية للطلاب وأولياء الأمور لمعرفة نسب الإنجاز وساعات التعلم.'],
                        ['title' => 'توليد تلقائي للشهادات الذكية', 'desc' => 'مكافأة الطلاب المتفوقين تلقائياً عند إنهاء الكورس بشهادات PDF احترافية باسمائهم.']
                    ]
                ],
                'tech_specs' => ['Next.js', 'React', 'Node.js', 'TailwindCSS', 'GraphQL', 'PostgreSQL'],
                'why_choose_us' => [
                    'en' => 'Robust video streaming integration, gamified learning dashboards, and seamless payment collection pipelines.',
                    'ar' => 'حماية تامة لمحتوى الفيديو من السرقة، تصميم تفاعلي ممتع للطلاب، ونظام دفع آمن مدمج.'
                ],
                'samples' => ["/portfolio/image-3.png", "/portfolio/image-4.png", "/portfolio/image-5.png", "/portfolio/image-6.png"]
            ],
            [
                'slug' => 'real-estate',
                'name' => ['en' => 'RealEstate Pro', 'ar' => 'عقارات برو'],
                'category' => ['en' => 'Real Estate', 'ar' => 'منصات التسويق والوساطة العقارية'],
                'title' => ['en' => 'RealEstate Pro System', 'ar' => 'منصة عقارات برو الاحترافية'],
                'description' => [
                    'en' => 'A powerful platform for real estate agencies to list properties, manage agents, and handle customer inquiries. Features an advanced map search, virtual tours integration, and a mobile-friendly design.',
                    'ar' => 'منصة "عقارات برو" هي الحل الرقمي الشامل لشركات التطوير العقاري والوسطاء. مجهزة بمحركات بحث جغرافية فائقة السرعة على الخريطة وميزات عرض متطورة، تجعل رحلة البحث عن منزل أو عقار تجاري سهلة وممتعة للغاية للعملاء.'
                ],
                'image' => '/portfolio/image-4.png',
                'demo_link' => '#',
                'order_link' => '#',
                'features' => [
                    'en' => [
                        ['title' => 'Interactive Spatial Maps', 'desc' => 'Locate houses and commercial properties instantly on a fluid GIS map.'],
                        ['title' => 'Virtual 3D Home Tours', 'desc' => 'Allow potential buyers to experience high-fidelity, interactive digital open houses.'],
                        ['title' => 'Automated Realtor CRM', 'desc' => 'Assign leads to agents, manage client inquiries, and track call histories easily.'],
                        ['title' => 'Mortgage & Cost Calculator', 'desc' => 'Built-in finance simulator that calculates monthly payments, deposits, and interest.']
                    ],
                    'ar' => [
                        ['title' => 'البحث التفاعلي بالخرائط', 'desc' => 'تحديد مواقع الفلل والشقق والمحلات التجارية جغرافياً على خريطة تفاعلية ذكية.'],
                        ['title' => 'جولات افتراضية ثلاثية الأبعاد', 'desc' => 'تمكين المشترين المحتملين من القيام بجولة حية وتفاعلية داخل العقار من هواتفهم.'],
                        ['title' => 'نظام إدارة علاقات العملاء (CRM)', 'desc' => 'توزيع طلبات العملاء تلقائياً على الوكلاء، ومتابعة التواصل وتنظيم المبيعات.'],
                        ['title' => 'حاسبة تمويل عقاري مدمجة', 'desc' => 'تتيح للعميل محاكاة الأقساط الشهرية، الدفعات الأولى، ونسب الفائدة تلقائياً.']
                    ]
                ],
                'tech_specs' => ['React', 'Next.js', 'TailwindCSS', 'Node.js', 'MongoDB', 'Mapbox API'],
                'why_choose_us' => [
                    'en' => 'SEO-friendly property directory, automated email alerts for new listings, and flawless responsiveness across mobile devices.',
                    'ar' => 'تأهيل كامل لمحركات البحث لزيادة مبيعات عقاراتك، نظام إشعارات فوري للعملاء بالفرص الجديدة، وتوافق تام مع الجوال.'
                ],
                'samples' => ["/portfolio/image-4.png", "/portfolio/image-5.png", "/portfolio/image-6.png", "/portfolio/image-1.png"]
            ],
            [
                'slug' => 'restaurant',
                'name' => ['en' => 'FoodieMenu', 'ar' => 'فودي منيو'],
                'category' => ['en' => 'Restaurant & Cafe', 'ar' => 'المنيو الرقمي والطلب الذاتي'],
                'title' => ['en' => 'FoodieMenu Ordering System', 'ar' => 'منصة فودي منيو المتكاملة للمطاعم'],
                'description' => [
                    'en' => 'A contactless menu and ordering system for restaurants. Customers can scan a QR code, view the interactive menu, place orders directly from their phones, and make payments online.',
                    'ar' => 'منصة "فودي منيو" هي نظام طلب ذاتي حديث يعتمد على رموز الـ QR ومصمم خصيصاً للمطاعم والمقاهي. تتيح المنصة لرواد مطعمك مسح الكود، طلب الوجبات، وتعديل إضافاتهم، ثم الدفع فوراً بكل سهولة ودون الحاجة لتنزيل أي تطبيق.'
                ],
                'image' => '/portfolio/image-5.png',
                'demo_link' => '#',
                'order_link' => '#',
                'features' => [
                    'en' => [
                        ['title' => 'No-Contact Table QR Ordering', 'desc' => 'Guests scan, order items, and checkout without waiting for a waiter.'],
                        ['title' => 'Vibrant Visual Menu Builder', 'desc' => 'Upload gorgeous pictures, display ingredient labels, and manage daily specials.'],
                        ['title' => 'Real-Time Kitchen KDS System', 'desc' => 'Direct order dispatch to kitchen screens ensuring lightning-fast preparation.'],
                        ['title' => 'Apple Pay & Wallet Integration', 'desc' => 'Seamless instant digital checkouts directly from the dining table.']
                    ],
                    'ar' => [
                        ['title' => 'الطلب الذاتي عبر QR الطاولة', 'desc' => 'يمسح العميل الكود، ويطلب ما يشاء مباشرة دون الحاجة لانتظار النادل لتسجيل الطلب.'],
                        ['title' => 'منيو تفاعلي مصور وجذاب', 'desc' => 'عرض أطباقك بصور عالية الدقة وتفاصيل المكونات، مع التحكم في إتاحة الأطباق يومياً.'],
                        ['title' => 'شاشة المطبخ الفورية (KDS)', 'desc' => 'إرسال الطلبات مباشرة لشاشات الطهاة في المطبخ لتسريع عملية التحضير وتقليل الأخطاء.'],
                        ['title' => 'تكامل الدفع الإلكتروني المباشر', 'desc' => 'دعم Apple Pay والبطاقات البنكية للدفع السريع والآمن مباشرة من الطاولة.']
                    ]
                ],
                'tech_specs' => ['Next.js', 'React', 'TailwindCSS', 'Socket.io', 'Firebase', 'Node.js'],
                'why_choose_us' => [
                    'en' => 'Increases average order value by up to 25% via smart upselling, minimizes waitstaff strain, and optimizes dining turnover.',
                    'ar' => 'يزيد متوسط قيمة الطلب بنسبة تصل لـ 25٪ بفضل الاقتراحات الذكية، ويوفر تكاليف المطبخ، ويسرع معدل تدوير الطاولات.'
                ],
                'samples' => ["/portfolio/image-5.png", "/portfolio/image-6.png", "/portfolio/image-1.png", "/portfolio/image-2.png"]
            ],
            [
                'slug' => 'gym',
                'name' => ['en' => 'FitLife', 'ar' => 'فت لايف'],
                'category' => ['en' => 'Fitness & Gym', 'ar' => 'إدارة الصالات والنوادي الرياضية'],
                'title' => ['en' => 'FitLife Gym Management', 'ar' => 'نظام فت لايف لإدارة مراكز اللياقة'],
                'description' => [
                    'en' => 'Manage your gym memberships, class schedules, and trainers efficiently. Members can book classes, track their fitness progress, and renew their subscriptions easily through the FitLife portal.',
                    'ar' => 'نظام "فت لايف" هو منصة برمجية متكاملة مخصصة لإدارة الصالات الرياضية ومراكز الصحة والجمال. ينظم النظام الاشتراكات، الجداول الزمنية للتمارين، وتتبع أداء المدربين والمنتسبين مع تقارير مالية تفصيلية ومؤتمتة.'
                ],
                'image' => '/portfolio/image-6.png',
                'demo_link' => '#',
                'order_link' => '#',
                'features' => [
                    'en' => [
                        ['title' => 'Dynamic Membership Plans', 'desc' => 'Create flexible subscription plans, auto-billing schedules, and digital gate entry.'],
                        ['title' => 'Interactive Class Booking', 'desc' => 'Let members book yoga, crossfit, or swimming sessions online through their portals.'],
                        ['title' => 'Trainer & Shift Schedulers', 'desc' => 'Distribute classes, schedule personal training shifts, and evaluate performance.'],
                        ['title' => 'Auto-Billing & Expiry Warnings', 'desc' => 'Reduce churn by sending subscription expiry warnings and bill collection invoices.']
                    ],
                    'ar' => [
                        ['title' => 'إدارة الاشتراكات والخطط المرنة', 'desc' => 'تخصيص باقات اشتراك مرنة، وتفعيل الفواتير المتكررة التلقائية، والدخول الإلكتروني.'],
                        ['title' => 'جدولة وحجز الحصص التدريبية', 'desc' => 'تمكين المشتركين من حجز حصص الكارديو، اليوغا، أو السباحة عبر بواباتهم بكل سهولة.'],
                        ['title' => 'بوابة المدربين والجداول الشخصية', 'desc' => 'تنظيم ورديات المدربين الشخصيين، وتعيين الحصص، وتتبع تقدم المتدربين معهم.'],
                        ['title' => 'تنبيهات وتذكيرات التجديد', 'desc' => 'نظام آلي لإرسال رسائل التذكير بقرب انتهاء الاشتراك والفواتير المعلقة لضمان الاستمرارية.']
                    ]
                ],
                'tech_specs' => ['React', 'Next.js', 'TailwindCSS', 'Node.js', 'MongoDB', 'Framer Motion'],
                'why_choose_us' => [
                    'en' => 'Includes check-in QR scanner system, client progress trackers, and detailed financial reports for gym management.',
                    'ar' => 'مزيد بنظام مسح باركود الدخول بالهاتف، وتتبع قياسات الجسم للأعضاء، مع لوحة إحصائيات مالية شاملة للإدارة.'
                ],
                'samples' => ["/portfolio/image-6.png", "/portfolio/image-1.png", "/portfolio/image-2.png", "/portfolio/image-3.png"]
            ]
        ];

        foreach ($productsData as $data) {
            Product::updateOrCreate(['slug' => $data['slug']], $data);
        }

        // 2. Seed Careers
        $jobsData = [
            [
                'title' => ['en' => 'Data Analyst', 'ar' => 'محلل بيانات'],
                'department' => 'Development',
                'location' => ['en' => 'Mansoura, Egypt', 'ar' => 'المنصورة، مصر'],
                'salary' => ['en' => 'Competitive Salary', 'ar' => 'راتب مجزي ومنافس'],
                'description' => [
                    'en' => 'We’re looking for a geek Data Analyst capable of working on establishing a strong data department able to guide the whole business to take data driven decisions.',
                    'ar' => 'نبحث عن محلل بيانات شغوف ومحترف لتأسيس قسم بيانات قوي وتوجيه الأعمال نحو اتخاذ قرارات مدروسة ومبنية على البيانات الفعالة.'
                ],
                'responsibilities' => [
                    'en' => [
                        'Track, collect, and interpret data, then analyze results using statistical techniques.',
                        'Identify, analyze and interpret trends or patterns in complex data sets.',
                        'Apply data-driven approaches to optimize business processes and efficiency.'
                    ],
                    'ar' => [
                        'تتبع وجمع وتفسير البيانات، ثم تحليل النتائج باستخدام التقنيات الإحصائية.',
                        'تحديد وتحليل وتفسير الاتجاهات أو الأنماط في مجموعات البيانات المعقدة.',
                        'تطبيق الحلول البرمجية القائمة على البيانات لتحسين كفاءة وجودة العمليات.'
                    ]
                ],
                'requirements' => [
                    'en' => [
                        'Strong experience in SQL, Python or R, and statistical analysis tools.',
                        'Familiarity with data visualization dashboards (Tableau, PowerBI or similar).',
                        'At least 2-3 years of proven experience as a Data Analyst.'
                    ],
                    'ar' => [
                        'خبرة قوية في SQL، ولغات البرمجة Python أو R، وأدوات التحليل الإحصائي.',
                        'دراية كاملة بأدوات عرض وتحليل البيانات مثل (Tableau أو PowerBI).',
                        'خبرة عملية لا تقل عن 2-3 سنوات كمحلل بيانات.'
                    ]
                ]
            ],
            [
                'title' => ['en' => 'Front End Developer', 'ar' => 'مطور واجهات أمامية'],
                'department' => 'Development',
                'location' => ['en' => 'Mansoura, Egypt', 'ar' => 'المنصورة، مصر'],
                'salary' => ['en' => 'Competitive Salary', 'ar' => 'راتب مجزي ومنافس'],
                'description' => [
                    'en' => 'Build reusable code and libraries for future use. Optimize applications for maximum speed and scalability. Implement new requirements, handle change requests, and fix bugs.',
                    'ar' => 'بناء أكواد ومكتبات برمجية قابلة لإعادة الاستخدام مستقبلاً، وتحسين سرعة وكفاءة تطبيقات الويب بشكل فائق. برمجة المتطلبات الجديدة وتصحيح المشاكل التقنية في مختلف الإصدارات.'
                ],
                'responsibilities' => [
                    'en' => [
                        'Develop clean, structured, and responsive UI components using React and Next.js.',
                        'Optimize web applications for maximum speed, scalability, and optimal UX.',
                        'Collaborate with backend developers and designers to integrate Rest APIs seamlessly.'
                    ],
                    'ar' => [
                        'تطوير واجهات مستخدم نظيفة، منظمة، ومتجاوبة تماماً باستخدام React وNext.js.',
                        'تحسين أداء تطبيقات الويب للوصول لأقصى سرعة وقابلية للتوسع.',
                        'التعاون مع مطوري الخلفية والمصممين لتكامل الـ APIs بسلاسة.'
                    ]
                ],
                'requirements' => [
                    'en' => [
                        'Proficient in HTML5, CSS3, JavaScript (ES6+), React, and Next.js.',
                        'Strong understanding of responsive web design, semantic HTML, and CSS layouts.',
                        'Experience in debugging and optimizing performance across multiple browsers.'
                    ],
                    'ar' => [
                        'إتقان تام لـ HTML5، CSS3، JavaScript (ES6+)، React، وNext.js.',
                        'فهم قوي لتصميمات الويب المتجاوبة وبنية الـ CSS والـ HTML الدلالية.',
                        'خبرة سابقة في تصحيح الأخطاء وتحسين الأداء عبر المتصفحات المختلفة.'
                    ]
                ]
            ],
            [
                'title' => ['en' => 'Graphic Designer', 'ar' => 'مصمم جرافيك'],
                'department' => 'Design',
                'location' => ['en' => 'Mansoura, Egypt', 'ar' => 'المنصورة، مصر'],
                'salary' => ['en' => 'Based on Experience', 'ar' => 'يحدد حسب الخبرة'],
                'description' => [
                    'en' => "We're looking for an amazing Graphic Designer who has the capability of creating premium designs that tell compelling visual stories for our services and products.",
                    'ar' => 'نحن نبحث عن مصمم مبدع ومحترف لديه القدرة على إنشاء تصميمات بصرية جذابة تروي قصة خدماتنا وتلهم مستخدمينا.'
                ],
                'responsibilities' => [
                    'en' => [
                        'Create visually striking social media designs, ad campaigns, and branding assets.',
                        'Conceptualize, develop, and implement digital graphics for product usability.',
                        'Collaborate with the marketing department to produce creative layouts that align with our brand identity.'
                    ],
                    'ar' => [
                        'إنشاء تصميمات مبتكرة للسوشيال ميديا، الحملات الإعلانية، وأصول الهوية البصرية.',
                        'تصور وتطوير وتنفيذ الرسومات الرقمية لزيادة سهولة استخدام المنتجات.',
                        'التعاون مع قسم التسويق لإنتاج تصميمات إبداعية تتماشى مع هوية الشركة.'
                    ]
                ],
                'requirements' => [
                    'en' => [
                        'Not less than 5 years of proven experience in graphic design.',
                        'Expert knowledge of Adobe Photoshop, Illustrator, and other design platforms.',
                        'A strong portfolio showcasing creative design layouts and visual excellence.'
                    ],
                    'ar' => [
                        'خبرة عملية لا تقل عن 5 سنوات في مجال التصميم الجغرافي والجرافيك.',
                        'معرفة احترافية ببرامج Adobe Photoshop وIllustrator والبرامج المماثلة.',
                        'معرض أعمال قوي يوضح المهارات الإبداعية وجودة التصميم.'
                    ]
                ]
            ],
            [
                'title' => ['en' => 'SEO Specialist', 'ar' => 'أخصائي سيو (SEO)'],
                'department' => 'Marketing',
                'location' => ['en' => 'Mansoura, Egypt', 'ar' => 'المنصورة، مصر'],
                'salary' => ['en' => 'Competitive Salary', 'ar' => 'راتب مجزي ومنافس'],
                'description' => [
                    'en' => 'Serv5 provides a job opportunity for an SEO specialist. If you would like to join us, contact us and send us your CV. A training opportunity is also available for passionate learners.',
                    'ar' => 'توفر سيرف فايف فرصة عمل لأخصائي سيو (SEO Specialist) ذو خبرة. إذا كنت ترغب في الانضمام إلينا، أرسل سيرتك الذاتية (فرصة تدريب متاحة للشغوفين بالتعلم).'
                ],
                'responsibilities' => [
                    'en' => [
                        'Perform thorough keyword research to optimize website page structures and blog content.',
                        'Track search engine ranking changes and optimize on-page/off-page SEO configurations.',
                        'Analyze competitor backlinks, site speed audits, and organic traffic metrics.'
                    ],
                    'ar' => [
                        'إجراء بحث شامل عن الكلمات المفتاحية لتحسين بنية المواقع والمدونات.',
                        'متابعة ترتيب صفحات الموقع في محركات البحث وتحسين الـ SEO الداخلي والخارجي.',
                        'تحليل الروابط الخلفية للمنافسين، فحص سرعة الموقع، ومؤشرات حركة المرور الطبيعية.'
                    ]
                ],
                'requirements' => [
                    'en' => [
                        'At least two years of solid experience in Search Engine Optimization.',
                        'Strong knowledge of Google Analytics, Search Console, Semrush, and SEO auditing tools.',
                        'High passion for digital marketing and learning search algorithm updates.'
                    ],
                    'ar' => [
                        'خبرة عملية لا تقل عن سنتين في تحسين محركات البحث.',
                        'دراية قوية بأدوات Google Analytics وSearch Console وSemrush وأدوات الفحص والتحليل.',
                        'شغف كبير بالتسويق الرقمي ومواكبة تحديثات خوارزميات محركات البحث.'
                    ]
                ]
            ],
            [
                'title' => ['en' => 'Motion Graphics Specialist', 'ar' => 'أخصائي موشن جرافيك'],
                'department' => 'Design',
                'location' => ['en' => 'Mansoura, Egypt', 'ar' => 'المنصورة، مصر'],
                'salary' => ['en' => 'Based on Experience', 'ar' => 'يحدد حسب الخبرة'],
                'description' => [
                    'en' => 'We at serv5 are looking for a Motion Graphics Specialist with a fertile imagination. Work requirements: creativity and punctuality.',
                    'ar' => 'نبحث عن أخصائي موشن جرافيك ذو خيال خصب وقدرة عالية على الابتكار لإنتاج مقاطع فيديو متحركة تسويقية وتعليمية مبهرة لمنتجاتنا وخدماتنا.'
                ],
                'responsibilities' => [
                    'en' => [
                        'Conceptualize, storyboard, and create high-fidelity motion graphics animations.',
                        'Develop high-quality product demos and educational/marketing video assets.',
                        'Coordinate timelines and work closely with directors and editors to deliver premium animations.'
                    ],
                    'ar' => [
                        'تصور وتصميم الستوري بورد ورسم وتصميم مقاطع الموشن جرافيك بجودة عالية.',
                        'تطوير فيديوهات تجريبية للمنتجات وعروض تسويقية وحزم تعليمية متميزة.',
                        'تنسيق جداول التسليم والعمل بشكل وثيق مع المخرجين والمحررين لتقديم حركات احترافية.'
                    ]
                ],
                'requirements' => [
                    'en' => [
                        'Not less than 5 years of proven experience in motion graphics design.',
                        'Highly proficient in Adobe After Effects, Premiere, and Illustrator.',
                        'Strong portfolio featuring premium animation work and storytelling capabilities.'
                    ],
                    'ar' => [
                        'خبرة عملية لا تقل عن 5 سنوات في تصميم وتحريك الموشن جرافيك.',
                        'إتقان تام لبرامج Adobe After Effects وPremiere وIllustrator.',
                        'معرض أعمال متميز يبرز مهارات التحريك وسرد القصص البصرية الاحترافية.'
                    ]
                ]
            ],
            [
                'title' => ['en' => 'Flutter Developer', 'ar' => 'مطور تطبيقات فلاتر (Flutter)'],
                'department' => 'Development',
                'location' => ['en' => 'Mansoura, Egypt', 'ar' => 'المنصورة، مصر'],
                'salary' => ['en' => 'Competitive Salary', 'ar' => 'راتب مجزي ومنافس'],
                'description' => [
                    'en' => 'We are looking for a Flutter developer to join us. Build user interfaces designed by UI/UX designers, integrate APIs, and handle MVVM/MVP patterns.',
                    'ar' => 'نبحث عن مطور تطبيقات Flutter للانضمام إلى فريقنا. متطلبات العمل: تصميم وتطوير تطبيقات متكاملة متعددة المنصات، وبناء واجهات مستخدم متميزة مبنية على تصميمات UI/UX، وتكامل قواعد البيانات والـ APIs المختلفة.'
                ],
                'responsibilities' => [
                    'en' => [
                        'Design, build, and deploy multi-platform applications using Flutter.',
                        'Integrate external REST APIs, third-party libraries, and secure storage options.',
                        'Implement state management solutions like Bloc, Provider, or Riverpod.'
                    ],
                    'ar' => [
                        'تصميم وبناء ونشر تطبيقات متعددة المنصات باستخدام إطار عمل Flutter.',
                        'تكامل الـ APIs الخارجية والمكتبات والحلول المتكاملة لتخزين البيانات وحمايتها.',
                        'تطبيق هياكل إدارة الحالة المتطورة مثل (Bloc أو Provider أو Riverpod).'
                    ]
                ],
                'requirements' => [
                    'en' => [
                        'Strong experience in Flutter, Dart, and cross-platform native behaviors.',
                        'Good understanding of MVC, MVVM, and MVP architectures.',
                        'Experience in publishing applications to the Apple App Store and Google Play Store.'
                    ],
                    'ar' => [
                        'خبرة قوية في لغة البرمجة Dart وإطار عمل Flutter وتعديل الخصائص الأصلية للمنصات.',
                        'فهم ممتاز لهندسة البرمجيات مثل MVC وMVVM وMVP.',
                        'خبرة سابقة في رفع ونشر التطبيقات على متاجر Apple App Store وGoogle Play.'
                    ]
                ]
            ],
            [
                'title' => ['en' => 'Backend PHP Developer (Laravel)', 'ar' => 'مطور خلفية PHP (لارافيل)'],
                'department' => 'Development',
                'location' => ['en' => 'Mansoura, Egypt', 'ar' => 'المنصورة، مصر'],
                'salary' => ['en' => '8,000 - 12,000 EGP', 'ar' => '8,000 - 12,000 جنيه مصري'],
                'description' => [
                    'en' => 'We are seeking Backend PHP Developers with Laravel framework experience who will be responsible for enhancing our Online platforms.',
                    'ar' => 'نبحث عن مطور خلفية PHP (لارافيل) للانضمام إلى فريقنا ذو الخبرة. المتطلبات: خبرة قوية في إطار عمل Laravel (3 سنوات على الأقل) وPHP مع MySQL (4 سنوات على الأقل)، وخبرة في تكامل الـ APIs وإدارة إصدارات الكود (Git).'
                ],
                'responsibilities' => [
                    'en' => [
                        'Write clean, fast, and structured PHP backend logic using Laravel framework.',
                        'Design scalable and secure database systems using MySQL/PostgreSQL.',
                        'Develop secure and optimized Web Services and RESTful APIs for mobile and web systems.'
                    ],
                    'ar' => [
                        'كتابة أكواد PHP خلفية نظيفة وسريعة وهيكلية باستخدام إطار عمل Laravel.',
                        'تصميم قواعد بيانات متطورة وآمنة وقابلة للتوسع باستخدام MySQL/PostgreSQL.',
                        'تطوير وبناء ويب سيرفس متكاملة وREST APIs مؤمنة لتطبيقات الجوال ومواقع الويب.'
                    ]
                ],
                'requirements' => [
                    'en' => [
                        'Strong experience in Laravel Framework (3 years at least) and PHP/MYSQL (4 years at least).',
                        'Familiarity with version control systems (Git) and collaborative team processes.',
                        'Understanding of Artificial Intelligence principles and API integrations is highly preferred.'
                    ],
                    'ar' => [
                        'خبرة لا تقل عن 3 سنوات في Laravel وخبرة لا تقل عن 4 سنوات في PHP/MYSQL.',
                        'دراية كاملة بأنظمة إدارة وتعديل الكود (Git) وطرق العمل الجماعية للفرق البرمجية.',
                        'فهم مبادئ الذكاء الاصطناعي وتكاملات الـ APIs المتقدمة يعتبر ميزة إضافية كبيرة.'
                    ]
                ]
            ],
            [
                'title' => ['en' => 'Human Resources Manager', 'ar' => 'مدير الموارد البشرية'],
                'department' => 'HR',
                'location' => ['en' => 'Mansoura, Egypt', 'ar' => 'المنصورة، مصر'],
                'salary' => ['en' => 'Competitive Salary', 'ar' => 'راتب مجزي ومنافس'],
                'description' => [
                    'en' => 'We are looking for an experienced human resources manager with a high ability to manage daily HR functions and create an optimal work environment for all employees.',
                    'ar' => 'نبحث عن مدير موارد بشرية (HR Manager) ذو خبرة عالية لإدارة العمل، وتوفير البيئة المناسبة للموظفين، ومتابعة الحضور والرواتب وعمليات التوظيف بكفاءة.'
                ],
                'responsibilities' => [
                    'en' => [
                        'Prepare, execute, and monitor the annual Manpower and hiring plan.',
                        'Oversee and manage employee attendance records, payroll workflows, and legal compliance.',
                        'Conduct initial screening interviews and onboard elite new talents into our system.'
                    ],
                    'ar' => [
                        'إعداد ومتابعة وتحديث خطة العمالة السنوية بالشركة وعمليات التوظيف.',
                        'متابعة سجلات الحضور والانصراف، وجداول الرواتب، والامتثال للوائح العمل بالشركة.',
                        'إجراء مقابلات التوظيف الأولية واختيار أفضل الكفاءات ودمجهم بنجاح داخل بيئة العمل.'
                    ]
                ],
                'requirements' => [
                    'en' => [
                        'Proven experience as an HR Manager or HR Executive for at least 3-5 years.',
                        'Excellent communication, organizational, and conflict-resolution skills.',
                        'In-depth knowledge of Egypt labor laws and modern HR practices.'
                    ],
                    'ar' => [
                        'خبرة لا تقل عن 3-5 سنوات كمدير للموارد البشرية أو مسؤول تنفيذي أول.',
                        'مهارات اتصال ممتازة، وقدرات تنظيمية وحل النزاعات بشكل احترافي.',
                        'معرفة عميقة بقوانين العمل واللوائح الإدارية للموارد البشرية الحديثة.'
                    ]
                ]
            ],
            [
                'title' => ['en' => 'Marketing Manager', 'ar' => 'مدير تسويق'],
                'department' => 'Marketing',
                'location' => ['en' => 'Mansoura, Egypt', 'ar' => 'المنصورة، مصر'],
                'salary' => ['en' => 'Competitive Salary', 'ar' => 'راتب مجزي ومنافس'],
                'description' => [
                    'en' => 'We are looking for a distinguished and advanced marketing manager to manage the social media department as well as the SEO department.',
                    'ar' => 'نبحث عن مدير تسويق متميز ومحترف لإدارة قسم السوشيال ميديا وقسم السيو وتطوير خطط تسويقية استراتيجية لزيادة الحصة السوقية للشركة وتوجيه الحملات الإعلانية.'
                ],
                'responsibilities' => [
                    'en' => [
                        'Formulate and execute dynamic marketing strategies to increase company market shares.',
                        'Oversee and guide the social media management team and the SEO department campaigns.',
                        'Analyze marketing channels performance, lead conversion rates, and ROI metrics.'
                    ],
                    'ar' => [
                        'صياغة وتنفيذ استراتيجيات تسويقية لزيادة الحصة السوقية للشركة.',
                        'الإشراف على وتوجيه فريق عمل السوشيال ميديا وحملات قسم السيو.',
                        'تحليل أداء قنوات التسويق المختلفة ومعدلات تحويل العملاء والعائد على الاستثمار.'
                    ]
                ],
                'requirements' => [
                    'en' => [
                        'Proven experience as a Marketing Manager or digital marketing strategist (3-5 years).',
                        'Strong knowledge of advertising campaigns management across multiple digital channels.',
                        'Highly skilled in developing marketing plans, analyzing analytics, and driving customer acquisition.'
                    ],
                    'ar' => [
                        'خبرة لا تقل عن 3-5 سنوات كمدير تسويق أو خبير استراتيجي في التسويق الرقمي.',
                        'دراية قوية بإدارة الحملات الإعلانية ومراقبتها على قنوات التواصل الرقمية المتعددة.',
                        'مهارة عالية في تطوير الخطط التسويقية، تحليل البيانات، وتحقيق أهداف الاستحواذ على العملاء.'
                    ]
                ]
            ],
            [
                'title' => ['en' => 'UI / UX Designer', 'ar' => 'مصمم واجهات وتجربة مستخدم (UI/UX)'],
                'department' => 'Design',
                'location' => ['en' => 'Mansoura, Egypt', 'ar' => 'المنصورة، مصر'],
                'salary' => ['en' => 'Based on Experience', 'ar' => 'يحدد حسب الخبرة'],
                'description' => [
                    'en' => 'We are looking for a UI/UX creator to join the team. Min 5+ years of experience in UX/UI designing of mobile/web applications.',
                    'ar' => 'نبحث عن مصمم واجهات وتجربة مستخدم (UI/UX Designer) للانضمام إلى فريقنا. خبرة لا تقل عن 5 سنوات في تصميم تطبيقات الجوال ومواقع الويب، وقدرة على توفير تجارب بصرية فريدة وسلسة للمستخدمين.'
                ],
                'responsibilities' => [
                    'en' => [
                        'Build highly interactive and premium wireframes, prototypes, and user flows.',
                        'Design polished and aesthetically-stunning user interfaces for mobile/web systems.',
                        'Collaborate with the frontend development team to verify the pixel-perfect implementation of UI screens.'
                    ],
                    'ar' => [
                        'بناء هياكل سلكية (Wireframes) وتدفقات مستخدم ونماذج أولية تفاعلية وممتازة.',
                        'تصميم واجهات مستخدم مذهلة وجمالية وعالية الدقة لتطبيقات الجوال ومواقع الويب.',
                        'التعاون مع مطوري الواجهات الأمامية لضمان مطابقة التصميم البرمجي مع التصميم الأصلي بدقة تامة.'
                    ]
                ],
                'requirements' => [
                    'en' => [
                        'BSC or Masters Degree in Computer Science, Design, Arts, or equivalent.',
                        'At least 5 years of solid experience in UI/UX design (Figma, Adobe XD, or similar).',
                        'A premium design portfolio demonstrating excellent typography, spacing, and micro-interactions.'
                    ],
                    'ar' => [
                        'درجة البكالوريوس أو الماجستير في علوم الحاسب أو التصميم أو الفنون أو ما يعادلها.',
                        'خبرة لا تقل عن 5 سنوات في تصميم واجهات وتجربة المستخدم (Figma أو Adobe XD).',
                        'معرض أعمال متميز يثبت الفهم العميق لأسس التخطيط، والخطوط، والمسافات، والتفاعل التفاعلي الدقيق.'
                    ]
                ]
            ]
        ];

        foreach ($jobsData as $data) {
            Career::updateOrCreate(['title->en' => $data['title']['en']], $data);
        }
    }
}
