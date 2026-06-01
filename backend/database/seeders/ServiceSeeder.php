<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'slug' => 'web-hosting',
                'icon' => '/Services/Purple Planet.webp',
                'is_active' => true,
                'name' => ['en' => 'Web Hosting', 'ar' => 'استضافة المواقع'],
                'description' => [
                    'en' => 'With us, you will get the most powerful website hosting that provides you with many advantages and helps you maintain the security of your site and keep it away from piracy',
                    'ar' => 'معنا ، ستحصل على أقوى استضافة مواقع توفر لك العديد من الميزات وتساعدك في الحفاظ على أمن موقعك وبقائه بعيداً عن القرصنة'
                ],
                'sections' => ['template' => 'template-three']
            ],
            [
                'slug' => 'digital-marketing',
                'icon' => '/Services/Blue Planet.webp',
                'is_active' => true,
                'name' => ['en' => 'Digital Marketing', 'ar' => 'التسويق الرقمي'],
                'description' => [
                    'en' => 'Expand your reach and grow your business with our cutting-edge digital marketing strategies tailored for your success.',
                    'ar' => 'وسع نطاق وصولك ونمِّ عملك من خلال استراتيجيات التسويق الرقمي المتطورة المصممة لنجاحك.'
                ],
                'sections' => ['template' => 'template-one']
            ],
            [
                'slug' => 'web-design',
                'icon' => '/Services/Yellow Planet.svg',
                'is_active' => true,
                'name' => ['en' => 'Web Design', 'ar' => 'تصميم المواقع'],
                'description' => [
                    'en' => 'Create stunning, responsive, and user-friendly websites that captivate your audience and represent your brand perfectly.',
                    'ar' => 'أنشئ مواقع ويب مذهلة وسريعة الاستجابة وسهلة الاستخدام تأسر جمهورك وتمثل علامتك التجارية بشكل مثالي.'
                ],
                'sections' => ['template' => 'template-two']
            ],
            [
                'slug' => 'creative-design',
                'icon' => '/Services/new/green.webp',
                'is_active' => true,
                'name' => ['en' => 'Creative Design', 'ar' => 'التصميم الإبداعي'],
                'description' => [
                    'en' => 'Bring your ideas to life with our creative design solutions that stand out and make a lasting impression.',
                    'ar' => 'اجعل أفكارك تنبض بالحياة من خلال حلول التصميم الإبداعي لدينا التي تبرز وتترك انطباعاً دائماً.'
                ],
                'sections' => ['template' => 'template-two']
            ],
        ];

        foreach ($services as $srv) {
            Service::updateOrCreate(['slug' => $srv['slug']], $srv);
        }
    }
}
