<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Faq;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        // Truncate existing FAQs to avoid duplicate seeding
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        Faq::truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        $faqsData = [
            [
                'question' => ['en' => 'What services does Serv5 specialize in?', 'ar' => 'ما هي الخدمات التي تتخصص فيها سيرف فايف؟'],
                'answer' => [
                    'en' => "Serv5 specializes in UI/UX Design, custom Web Development (React, Next.js, Laravel), Mobile App Development (Flutter), Search Engine Optimization (SEO), Social Media Marketing, and Premium Web Hosting.",
                    'ar' => "تتخصص سيرف فايف في تصميم واجهات وتجربة المستخدم (UI/UX)، تطوير الويب المخصص (React، Next.js، Laravel)، تطوير تطبيقات الجوال (Flutter)، تحسين محركات البحث (SEO)، التسويق عبر وسائل التواصل الاجتماعي، والاستضافة الممتازة."
                ]
            ],
            [
                'question' => ['en' => 'How long does it take to build a custom website?', 'ar' => 'كم من الوقت يستغرق بناء موقع ويب مخصص؟'],
                'answer' => [
                    'en' => "A standard landing page or portfolio website takes 1-2 weeks. Complex web applications, e-commerce platforms, or custom SaaS solutions typically take 4-8 weeks, depending on requirements.",
                    'ar' => "يستغرق تصميم صفحة هبوط بسيطة أو موقع أعمال من أسبوع إلى أسبوعين. بينما تحتاج تطبيقات الويب المعقدة، منصات التجارة الإلكترونية، أو حلول SaaS المخصصة عادة من 4 إلى 8 أسابيع حسب المتطلبات."
                ]
            ],
            [
                'question' => ['en' => 'Do you provide maintenance and updates post-launch?', 'ar' => 'هل تقدمون خدمات الصيانة والتحديثات بعد الإطلاق؟'],
                'answer' => [
                    'en' => "Yes, we offer flexible post-launch technical support and maintenance packages. We cover security monitoring, regular updates, bug fixes, and feature additions.",
                    'ar' => "نعم، نحن نقدم باقات صيانة ودعم فني مرنة بعد الإطلاق. تشمل باقاتنا مراقبة الأمان، التحديثات الدورية، تصحيح الأخطاء، وإضافة ميزات جديدة."
                ]
            ],
            [
                'question' => ['en' => 'Is my website going to be mobile-friendly?', 'ar' => 'هل سيكون موقعي متوافقاً مع الهواتف الذكية؟'],
                'answer' => [
                    'en' => "Absolutely. Every website we build features a responsive layout designed to load and function flawlessly on smartphones, tablets, laptops, and desktops.",
                    'ar' => "بالتأكيد. جميع المواقع التي نقوم بتطويرها تتميز بتصميم متجاوب بالكامل للعمل والعرض بشكل ممتاز على الهواتف، الأجهزة اللوحية، الحواسيب المحمولة، والمكتبية."
                ]
            ],
            [
                'question' => ['en' => 'Do you build native or cross-platform mobile apps?', 'ar' => 'هل تقومون ببناء تطبيقات جوال أصلية (Native) أم متعددة المنصات؟'],
                'answer' => [
                    'en' => "We primarily use Flutter for cross-platform app development, allowing us to build high-performance apps for both iOS and Android from a single codebase, saving you time and cost.",
                    'ar' => "نحن نعتمد بشكل أساسي على إطار عمل Flutter لتطوير التطبيقات متعددة المنصات، مما يتيح لنا بناء تطبيقات عالية الأداء لنظامي iOS وAndroid باستخدام كود موحد، مما يوفر الوقت والتكلفة."
                ]
            ],
            [
                'question' => ['en' => 'Can you redesign our outdated website?', 'ar' => 'هل يمكنك إعادة تصميم موقعنا القديم؟'],
                'answer' => [
                    'en' => "Yes! We can audit your current website's UX, speed, and visuals, then completely revamp it into a modern, faster, and highly-converting platform.",
                    'ar' => "نعم! يمكننا فحص تجربة المستخدم وسرعة ومظهر موقعك الحالي، ثم إعادة بنائه وتحديثه بالكامل ليصبح منصة عصرية، أسرع، وأعلى كفاءة في جذب العملاء."
                ]
            ],
            [
                'question' => ['en' => 'What is the cost of building a web application?', 'ar' => 'ما هي تكلفة بناء تطبيق ويب مخصص؟'],
                'answer' => [
                    'en' => "Costs vary depending on the complexity of features, database structure, and integration. Contact us to schedule a free consultation, and we will prepare a customized quote.",
                    'ar' => "تختلف التكلفة بناءً على تعقيد الميزات، هيكل قاعدة البيانات، والتكاملات المطلوبة. تواصل معنا لجدولة استشارة مجانية وسنعد لك عرض سعر مخصص."
                ]
            ],
            [
                'question' => ['en' => 'Do you provide web hosting and domain registration?', 'ar' => 'هل تقدمون خدمات الاستضافة وحجز النطاق؟'],
                'answer' => [
                    'en' => "Yes, we provide ultra-fast cloud hosting services with 99.9% uptime, daily backups, DDoS protection, and SSL certificates, along with domain registration support.",
                    'ar' => "نعم، نحن نوفر خدمات استضافة سحابية فائقة السرعة مع ضمان استقرار بنسبة 99.9%، نسخ احتياطي يومي، حماية من هجمات DDoS، وشهادات SSL مجانية، بالإضافة لحجز النطاقات."
                ]
            ],
            [
                'question' => ['en' => 'How does the project consultation work?', 'ar' => 'كيف تسير عملية استشارة المشروع؟'],
                'answer' => [
                    'en' => "You fill out our project consultation form. Our software engineers and business experts will review your request, set up a call, and outline a development strategy.",
                    'ar' => "تقوم بملء نموذج طلب الاستشارة الخاص بنا. سيقوم مهندسو البرمجيات وخبراء الأعمال بمراجعة طلبك، ثم ترتيب اتصال معك لتحديد استراتيجية التطوير المناسبة."
                ]
            ],
            [
                'question' => ['en' => 'Will my website rank high on search engines (SEO)?', 'ar' => 'هل سيظهر موقعي في المراتب الأولى بمحركات البحث (SEO)؟'],
                'answer' => [
                    'en' => "We implement technical SEO best practices (semantic HTML, fast load speeds, meta structures, and schema markup) into our core code to ensure search engines index your site optimally.",
                    'ar' => "نحن نطبق أفضل ممارسات السيو التقني (بنية HTML الدلالية، سرعات التحميل الفائقة، والبيانات المنظمة) في الكود الأساسي لضمان أرشفة موقعك بشكل مثالي."
                ]
            ],
            [
                'question' => ['en' => 'Do you build custom e-commerce stores?', 'ar' => 'هل تقومون ببناء متاجر تجارة إلكترونية مخصصة؟'],
                'answer' => [
                    'en' => "Yes, we develop custom e-commerce solutions that handle unlimited products, secure online payment gateway integrations (Apple Pay, Stripe, local wallets), and inventory control hubs.",
                    'ar' => "نعم، نحن نطور حلول تجارة إلكترونية مخصصة تستوعب عدداً غير محدود من المنتجات، مع دمج بوابات الدفع الإلكتروني الآمنة ولوحات التحكم الذكية للمخزون."
                ]
            ],
            [
                'question' => ['en' => 'Can you build dashboard systems for custom data management?', 'ar' => 'هل يمكنكم بناء لوحات تحكم لإدارة البيانات المخصصة؟'],
                'answer' => [
                    'en' => "Yes, we design and build sleek, premium administrative dashboards (using Filament, Laravel, or React) that visualize complex operations, reports, and statistics in real-time.",
                    'ar' => "نعم، نحن نصمم ونبني لوحات تحكم إدارية راقية وسلسة (باستخدام Filament أو Laravel أو React) لعرض العمليات المعقدة والتقارير والإحصاءات في الوقت الفعلي."
                ]
            ],
            [
                'question' => ['en' => 'What technologies do you use for backend development?', 'ar' => 'ما هي التقنيات التي تستخدمونها لتطوير الخلفية (Backend)؟'],
                'answer' => [
                    'en' => "We primarily use PHP (Laravel framework) and Node.js for building scalable, secure API servers, with databases like MySQL, PostgreSQL, and MongoDB.",
                    'ar' => "نحن نستخدم بشكل أساسي لغة PHP (إطار عمل Laravel) وNode.js لبناء خوادم الـ API الآمنة والقابلة للتوسع، مع قواعد بيانات مثل MySQL وPostgreSQL وMongoDB."
                ]
            ],
            [
                'question' => ['en' => 'Do you provide logo and brand identity design services?', 'ar' => 'هل تقدمون خدمات تصميم الشعار والهوية البصرية للعلامة التجارية؟'],
                'answer' => [
                    'en' => "Yes, our creative graphic design team crafts premium logo concepts, complete brand guidelines, typography systems, and color palettes that make your company stand out.",
                    'ar' => "نعم، يقوم فريق التصميم الجرافيكي لدينا بابتكار شعارات مميزة، وتطوير أدلة الهوية البصرية الكاملة، والخطوط، وتناسق الألوان لتميز شركتك."
                ]
            ],
            [
                'question' => ['en' => 'What is waiting for me in the project workflow?', 'ar' => 'ما الذي ينتظرني في مراحل سير المشروع؟'],
                'answer' => [
                    'en' => "Our workflow goes from Strategy & Analysis, to Wireframing & UX, UI Design, Development & Coding, QA Testing, and finally Launch & Post-support.",
                    'ar' => "يمر مشروعنا بمراحل التحليل والاستراتيجية، ثم رسم مخططات تجربة المستخدم، وتصميم الواجهات، والبرمجة والتطوير، واختبار الجودة، ثم الإطلاق والدعم."
                ]
            ],
            [
                'question' => ['en' => 'Can I easily edit my site content after release?', 'ar' => 'هل يمكنني تعديل محتوى موقعي بسهولة بعد إطلاقه؟'],
                'answer' => [
                    'en' => "Yes, we configure intuitive Content Management Systems (CMS) or admin panels (like Filament) so you can easily update texts, images, blogs, and products without writing code.",
                    'ar' => "نعم، نحن نقوم بتهيئة لوحات تحكم سهلة الاستخدام (مثل Filament) لتمكينك من تعديل النصوص، الصور، المقالات، والمنتجات دون الحاجة لكتابة كود برميجي."
                ]
            ],
            [
                'question' => ['en' => 'Do you support multi-lingual sites?', 'ar' => 'هل تدعمون المواقع متعددة اللغات؟'],
                'answer' => [
                    'en' => "Yes, multi-lingual support (particularly English and Arabic) is a core standard for all our applications. We translate layout structures, navigation details, and database inputs.",
                    'ar' => "نعم، دعم اللغات المتعددة (خاصة الإنجليزية والعربية) هو معيار أساسي في جميع تطبيقاتنا. نقوم بتهيئة الهياكل والترجمات ديناميكياً."
                ]
            ],
            [
                'question' => ['en' => 'Is our application protected from cybersecurity attacks?', 'ar' => 'هل سيكون تطبيقناً محمياً من الهجمات السيبرانية؟'],
                'answer' => [
                    'en' => "We implement strict security configurations: SQL injection protection, CSRF defense mechanisms, request rate-limiting, and encrypted databases to guarantee high security.",
                    'ar' => "نحن نطبق معايير أمان صارمة: حماية من حقن SQL، آليات دفاع ضد هجمات CSRF، تحديد معدل الطلبات، وتشفير البيانات الحساسة لضمان أمان كامل."
                ]
            ],
            [
                'question' => ['en' => 'Do you integrate AI models into your platforms?', 'ar' => 'هل تقومون بدمج نماذج الذكاء الاصطناعي (AI) في منصاتكم؟'],
                'answer' => [
                    'en' => "Yes, we integrate OpenAI APIs, Custom LLMs, chatbot systems, and smart automation workflows into Next.js/Laravel systems according to business automation needs.",
                    'ar' => "نعم، نقوم بدمج واجهات برمجة تطبيقات OpenAI، النماذج اللغوية الكبيرة المخصصة، أنظمة المساعد الذكي، وأتمتة العمليات البرمجية في أنظمتنا حسب احتياجاتك."
                ]
            ],
            [
                'question' => ['en' => 'How can we get started with Serv5?', 'ar' => 'كيف يمكننا البدء بالعمل مع سيرف فايف؟'],
                'answer' => [
                    'en' => "Simply navigate to our Contact Us page, call our hotline, or request a free consultation. Our team will reach out to analyze your idea and start our journey together.",
                    'ar' => "ما عليك سوى الانتقال لصفحة اتصل بنا، الاتصال بنا هاتفياً، أو طلب استشارة مجانية. سيتواصل معك فريقنا لتحليل فكرتك والبدء في رحلتنا معاً."
                ]
            ],
        ];

        foreach ($faqsData as $faq) {
            Faq::create([
                'question' => $faq['question'],
                'answer' => $faq['answer'],
                'is_active' => true,
            ]);
        }
    }
}
