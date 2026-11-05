<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Faq::truncate();
        Schema::enableForeignKeyConstraints();

        // 1. Official FAQs from serv5.com XML (Page 5897 AR & Page 10798 EN)
        $wpFaqFile = database_path('seeders/data/wp_faqs.json');
        $faqsData = [];
        if (file_exists($wpFaqFile)) {
            $loaded = json_decode((string) file_get_contents($wpFaqFile), true);
            if (is_array($loaded)) {
                $faqsData = $loaded;
            }
        }

        // 2. Additional detailed bilingual FAQs covering Serv5 services
        $extraFaqs = [
            [
                'question' => ['en' => 'Do you build native or cross-platform mobile apps?', 'ar' => 'هل تقومون ببرمجة تطبيقات الجوال لنظامي Android و iOS؟'],
                'answer' => [
                    'en' => 'Yes, at Serv5 we develop native and cross-platform mobile applications (Flutter, Android, iOS) with high performance, modern UI/UX, and full deployment to Google Play and Apple App Store.',
                    'ar' => 'نعم، في سيرف فايف نقوم ببرمجة وتطوير تطبيقات الجوال المتكاملة لنظامي Android و iOS بأحدث التقنيات، مع تصميم واجهات عصرية ورفع التطبيقات على متجري Google Play و App Store.',
                ],
            ],
            [
                'question' => ['en' => 'Do you provide web hosting and domain registration?', 'ar' => 'هل تقدمون خدمات استضافة المواقع وحجز النطاقات (الدومين)؟'],
                'answer' => [
                    'en' => 'Yes, Serv5 provides high-speed cloud hosting plans (Silver, Golden, Bronze, and Featured Serv) with 99.99% uptime, cPanel control panel, free site migration, and continuous security protection.',
                    'ar' => 'نعم، توفر سيرف فايف باقات استضافة مواقع قوية (سيرف الفضي، الذهبي، البرونزي، والمميز) تعمل بنسبة استقرار 99.99% مع لوحة تحكم cPanel، نقل الموقع مجاناً، وحماية طوال فترة الاشتراك.',
                ],
            ],
            [
                'question' => ['en' => 'Will my website rank high on search engines (SEO)?', 'ar' => 'هل تقدمون خدمات تهيئة المواقع لمحركات البحث (SEO)؟'],
                'answer' => [
                    'en' => 'Yes, we offer dedicated Search Engine Optimization (SEO) and Google indexing services, including keyword research, technical SEO optimization, content management, and backlink strategies.',
                    'ar' => 'نعم، نقدم خدمات أرشفة وتهيئة المواقع في محركات البحث (SEO) للظهور في النتائج الأولى بجوجل من خلال استهداف الكلمات المفتاحية، تحسين سرعة وبنية الموقع، وإدارة المحتوى الرقمي.',
                ],
            ],
            [
                'question' => ['en' => 'Do you design custom e-commerce online stores?', 'ar' => 'هل تقومون بتصميم وبرمجة المتاجر الإلكترونية؟'],
                'answer' => [
                    'en' => 'Yes, Serv5 designs and develops integrated online stores with multi-currency support, electronic payment gateways, shipping integrations, and Arabic/English control panels.',
                    'ar' => 'نعم، نصمم ونبرمج متاجر إلكترونية متكاملة تدعم تعدد اللغات والعملات، الربط مع بوابات الدفع الإلكتروني وشركات الشحن، مع لوحة تحكم سهلة لإدارة المنتجات والطلبات.',
                ],
            ],
            [
                'question' => ['en' => 'Do you provide motion graphics and digital identity services?', 'ar' => 'هل تقدمون خدمات تصميم الموشن جرافيك والهوية الرقمية؟'],
                'answer' => [
                    'en' => 'Yes, our creative studio produces professional 2D/3D motion graphics videos (scenario writing, voiceover, and animation) as well as complete corporate digital identities and logos.',
                    'ar' => 'نعم، يضم فريقنا قسماً متكاملاً لتصميم فيديوهات الموشن جرافيك الاحترافية (كتابة السيناريو، التعليق الصوتي، والتحريك) بالإضافة إلى تصميم الشعارات والهويات التجارية والرقمية الكاملة.',
                ],
            ],
            [
                'question' => ['en' => 'What payment methods and bank accounts does Serv5 accept?', 'ar' => 'ما هي طرق الدفع والحسابات البنكية المتاحة لدى سيرف فايف؟'],
                'answer' => [
                    'en' => 'We support bank transfers inside Egypt (National Bank of Egypt, Banque Misr, Crédit Agricole, United Bank, Egypt Post, Vodafone/Etisalat/Orange Cash) and from Saudi Arabia & Worldwide (Al Rajhi Bank, SNB Al-Ahly, Bank Albilad, Western Union, MoneyGram).',
                    'ar' => 'نوفر طرق دفع مرنة داخل مصر (البنك الأهلي المصري، بنك مصر، كريدي أجريكول، المصرف المتحد، البريد المصري، فودافون واتصالات وأورانج كاش) ومن السعودية وخارج مصر (مصرف الراجحي، البنك الأهلي، بنك البلاد، ويسترن يونيون، وموني جرام).',
                ],
            ],
        ];

        foreach (array_merge($faqsData, $extraFaqs) as $faq) {
            Faq::create([
                'question' => $faq['question'],
                'answer' => $faq['answer'],
                'is_active' => true,
            ]);
        }
    }
}
