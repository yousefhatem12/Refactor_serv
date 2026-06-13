<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Page;

// 1. About Us
Page::updateOrCreate(
    ['slug' => 'about-us'],
    [
        'title' => [
            'en' => 'Proven Track Record of Success',
            'ar' => 'سجل حافل بالنجاحات',
        ],
        'subtitle' => [
            'en' => 'Serv5 brings an unmatched legacy of innovation and excellence to your business. Our track record speaks for itself.',
            'ar' => 'تقدم Serv5 إرثاً لا يضاهى من الابتكار والتميز لعملك. سجلنا الحافل يتحدث عن نفسه.',
        ],
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
                        'content' => "نحن نتشارك مع الشركات لتحديد التحديات الخاصة بهم وصياغة حلول مبتكرة مصممة وفقاً لمتطلباتهم الدقيقة.\n\nمن خلال الاستفادة من أحدث التقنيات ومنهجيات التطوير الرشيقة (Agile)، نضمن أن حلولنا لا تلبي احتياجاتك الحالية فحسب.",
                        'layout' => 'text_right',
                    ]
                ],
                [
                    'type' => 'features',
                    'data' => [
                        'title' => 'لماذا تختارنا؟',
                        'items' => [
                            ['title' => 'شريك موثوق', 'description' => 'نحن شريك موثوق في دفع التحول الرقمي.', 'icon' => 'FaHandshake'],
                            ['title' => 'حلول مبتكرة', 'description' => 'نقدم حلولاً مبتكرة ومخصصة تدفع نمو الأعمال.', 'icon' => 'FaLightbulb'],
                            ['title' => 'فريق خبير', 'description' => 'يتكون فريقنا من خبراء ذوي مهارات عالية.', 'icon' => 'FaUsers'],
                            ['title' => 'التركيز على العميل', 'description' => 'نضع احتياجات عملائنا في المقام الأول.', 'icon' => 'FaHeart'],
                            ['title' => 'منهجية مثبتة', 'description' => 'نستخدم منهجية مثبتة للنجاح، تجمع بين الممارسات الرشيقة.', 'icon' => 'FaChartLine'],
                            ['title' => 'دعم مستمر', 'description' => 'نقدم دعماً وصيانة مستمرة لضمان الأداء الأمثل.', 'icon' => 'FaHeadset'],
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
                ]
            ],
        ],
    ]
);

// 2. Contact Us
Page::updateOrCreate(
    ['slug' => 'contact-us'],
    [
        'title' => [
            'en' => 'Let\'s Get in Touch',
            'ar' => 'ابقى على تواصل',
        ],
        'subtitle' => [
            'en' => 'Enim tempor eget pharetra facilisis sed maecenas adipiscing. Eu leo molestie vel, ornare non id blandit netus.',
            'ar' => 'تواصل معنا لمناقشة أفكارك وتطلعاتك. نحن هنا لمساعدتك على تحقيق أهدافك بأفضل صورة ممكنة.',
        ],
        'sections' => [
            'en' => [],
            'ar' => []
        ]
    ]
);

echo "Pages seeded successfully.\n";
////cs