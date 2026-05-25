<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Testimonial;
use Illuminate\Support\Facades\Schema;

class TestimonialSeeder extends Seeder
{
    public function run()
    {
        // Clear existing testimonials
        Schema::disableForeignKeyConstraints();
        Testimonial::truncate();
        Schema::enableForeignKeyConstraints();

        $testimonials = [
            [
                'client_name' => ['en' => 'Sarah Jenkins', 'ar' => 'سارة جينكينز'],
                'position' => ['en' => 'Product Manager at TechFlow', 'ar' => 'مديرة المنتجات في تك فلو'],
                'review' => [
                    'en' => 'Working with Serv5 was an absolute pleasure. They delivered a high-quality SaaS product on time, with excellent communication throughout the process.',
                    'ar' => 'كان العمل مع Serv5 متعة حقيقية. لقد قدموا منتج SaaS عالي الجودة في الوقت المحدد، مع تواصل ممتاز طوال فترة العمل.'
                ],
                'image' => '/about-us/person-1.png',
                'rating' => 5,
                'video_url' => 'https://www.w3schools.com/html/mov_bbb.mp4',
                'audio_url' => null,
                'is_active' => true,
            ],
            [
                'client_name' => ['en' => 'Khalid Al-Mansoor', 'ar' => 'خالد المنصور'],
                'position' => ['en' => 'Founder of Diyar Real Estate', 'ar' => 'مؤسس شركة ديار العقارية'],
                'review' => [
                    'en' => 'The custom real estate dashboard developed by Serv5 transformed our operations. Our agents can now manage listings twice as fast.',
                    'ar' => 'لوحة التحكم العقارية المخصصة التي طورتها Serv5 أحدثت تحولاً في عملياتنا. يمكن لوكلائنا الآن إدارة العروض بسرعة مضاعفة.'
                ],
                'image' => '/about-us/person-2.png',
                'rating' => 5,
                'video_url' => null,
                'audio_url' => null,
                'is_active' => true,
            ],
            [
                'client_name' => ['en' => 'Amira Youssef', 'ar' => 'أميرة يوسف'],
                'position' => ['en' => 'Marketing Director at EcoGreens', 'ar' => 'مديرة التسويق في إيكو جرينز'],
                'review' => [
                    'en' => 'The brand guidelines and packaging designs created by Serv5 captured our vision perfectly. We received amazing feedback from our clients.',
                    'ar' => 'تصاميم الهوية وتغليف المنتجات التي ابتكرتها Serv5 جسدت رؤيتنا بشكل مثالي. لقد تلقينا ردود فعل مذهلة من عملائنا.'
                ],
                'image' => '/about-us/person-3.png',
                'rating' => 5,
                'video_url' => null,
                'audio_url' => null,
                'is_active' => true,
            ],
            [
                'client_name' => ['en' => 'John Carter', 'ar' => 'جون كارتر'],
                'position' => ['en' => 'CTO of QuickExpress', 'ar' => 'المدير التقني لشركة كويك إكسبريس'],
                'review' => [
                    'en' => 'Their Flutter mobile app development skills are top-notch. The Rider app performs flawlessly even in low network areas.',
                    'ar' => 'مهاراتهم في تطوير تطبيقات الجوال بنظام Flutter ممتازة للغاية. يعمل تطبيق المناديب بكفاءة عالية حتى في المناطق ذات الشبكة الضعيفة.'
                ],
                'image' => '/about-us/person-1.png',
                'rating' => 4,
                'video_url' => null,
                'audio_url' => 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3',
                'is_active' => true,
            ],
        ];

        foreach ($testimonials as $tData) {
            Testimonial::create($tData);
        }
    }
}
