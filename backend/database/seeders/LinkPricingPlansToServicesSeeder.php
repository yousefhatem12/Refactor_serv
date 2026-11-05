<?php

namespace Database\Seeders;

use App\Models\PricingPlan;
use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class LinkPricingPlansToServicesSeeder extends Seeder
{
    public function run(): void
    {
        $hostingService = Service::where('slug', 'web-hosting')->first() ?? Service::first();
        if (! $hostingService) {
            return;
        }

        Schema::disableForeignKeyConstraints();
        PricingPlan::truncate();
        Schema::enableForeignKeyConstraints();

        $plans = [
            [
                'service_id' => $hostingService->id,
                'name' => ['en' => 'Silver Serv', 'ar' => 'سيرف الفضى'],
                'price' => '180',
                'currency' => '$',
                'period' => ['en' => 'annually', 'ar' => 'سنوياً'],
                'features' => [
                    ['en' => 'Disk Space: 5 GB', 'ar' => 'المساحة : 5 جيجا'],
                    ['en' => 'Unlimited traffic', 'ar' => 'ترافيك غير محدود'],
                    ['en' => 'Unlimited databases', 'ar' => 'قواعد بيانات غير محدودة'],
                    ['en' => 'Only one domain hosting allowed', 'ar' => 'مسموح باستضافة نطاق واحد'],
                    ['en' => 'The rest of the features are unlimited', 'ar' => 'باقي الخواص غير محدودة'],
                    ['en' => 'Cpanel control panel', 'ar' => 'لوحة تحكم سي بنل (cPanel)'],
                    ['en' => 'Free site transfer', 'ar' => 'نقل الموقع مجاناً'],
                    ['en' => 'Protection throughout the subscription period', 'ar' => 'حماية طوال فترة الاشتراك'],
                ],
                'is_popular' => false,
                'is_active' => true,
                'button_text' => ['en' => 'Order Now', 'ar' => 'اطلب الآن'],
                'button_url' => 'https://wa.me/201288863631',
                'sort_order' => 1,
            ],
            [
                'service_id' => $hostingService->id,
                'name' => ['en' => 'Golden Serv', 'ar' => 'سيرف ذهبى'],
                'price' => '270',
                'currency' => '$',
                'period' => ['en' => 'annually', 'ar' => 'سنوياً'],
                'features' => [
                    ['en' => 'Disk Space: 10 GB', 'ar' => 'المساحة : 10 جيجا'],
                    ['en' => 'Unlimited traffic', 'ar' => 'ترافيك غير محدود'],
                    ['en' => 'Unlimited databases', 'ar' => 'قواعد بيانات غير محدودة'],
                    ['en' => 'Only one domain hosting allowed', 'ar' => 'مسموح باستضافة نطاق واحد'],
                    ['en' => 'The rest of the features are unlimited', 'ar' => 'باقي الخواص غير محدودة'],
                    ['en' => 'Cpanel control panel', 'ar' => 'لوحة تحكم سي بنل (cPanel)'],
                    ['en' => 'Free site transfer', 'ar' => 'نقل الموقع مجاناً'],
                    ['en' => 'Protection throughout the subscription period', 'ar' => 'حماية طوال فترة الاشتراك'],
                ],
                'is_popular' => true,
                'is_active' => true,
                'button_text' => ['en' => 'Order Now', 'ar' => 'اطلب الآن'],
                'button_url' => 'https://wa.me/201288863631',
                'sort_order' => 2,
            ],
            [
                'service_id' => $hostingService->id,
                'name' => ['en' => 'Bronze Serv', 'ar' => 'سيرف برونزى'],
                'price' => '350',
                'currency' => '$',
                'period' => ['en' => 'annually', 'ar' => 'سنوياً'],
                'features' => [
                    ['en' => 'Disk Space: 20 GB', 'ar' => 'المساحة : 20 جيجا'],
                    ['en' => 'Unlimited traffic', 'ar' => 'ترافيك غير محدود'],
                    ['en' => 'Unlimited databases', 'ar' => 'قواعد بيانات غير محدودة'],
                    ['en' => 'Only one domain hosting allowed', 'ar' => 'مسموح باستضافة نطاق واحد'],
                    ['en' => 'Free domain included', 'ar' => 'نطاق مجاني للسنة الأولى'],
                    ['en' => 'Cpanel control panel', 'ar' => 'لوحة تحكم سي بنل (cPanel)'],
                    ['en' => 'Free site transfer', 'ar' => 'نقل الموقع مجاناً'],
                    ['en' => 'Protection throughout the subscription period', 'ar' => 'حماية طوال فترة الاشتراك'],
                ],
                'is_popular' => false,
                'is_active' => true,
                'button_text' => ['en' => 'Order Now', 'ar' => 'اطلب الآن'],
                'button_url' => 'https://wa.me/201288863631',
                'sort_order' => 3,
            ],
            [
                'service_id' => $hostingService->id,
                'name' => ['en' => 'Featured Serv', 'ar' => 'سيرف مميز'],
                'price' => '500',
                'currency' => '$',
                'period' => ['en' => 'annually', 'ar' => 'سنوياً'],
                'features' => [
                    ['en' => 'Unlimited Disk Space', 'ar' => 'المساحة غير محدودة'],
                    ['en' => 'Unlimited traffic', 'ar' => 'ترافيك غير محدود'],
                    ['en' => 'Unlimited databases', 'ar' => 'قواعد بيانات غير محدودة'],
                    ['en' => 'Free domain included', 'ar' => 'نطاق مجاني للسنة الأولى'],
                    ['en' => 'The rest of the features are unlimited', 'ar' => 'باقي الخواص غير محدودة'],
                    ['en' => 'Cpanel control panel', 'ar' => 'لوحة تحكم سي بنل (cPanel)'],
                    ['en' => 'Free site transfer', 'ar' => 'نقل الموقع مجاناً'],
                    ['en' => 'Protection throughout the subscription period', 'ar' => 'حماية طوال فترة الاشتراك'],
                ],
                'is_popular' => false,
                'is_active' => true,
                'button_text' => ['en' => 'Order Now', 'ar' => 'اطلب الآن'],
                'button_url' => 'https://wa.me/201288863631',
                'sort_order' => 4,
            ],
        ];

        foreach ($plans as $plan) {
            PricingPlan::create($plan);
        }
    }
}
