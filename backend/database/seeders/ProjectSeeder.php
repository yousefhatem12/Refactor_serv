<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;

class ProjectSeeder extends Seeder
{
    public function run()
    {
        $projects = [
            [
                'name' => ['en' => 'E-Commerce Platform', 'ar' => 'منصة تجارة إلكترونية'],
                'category' => 'websites',
                'description' => ['en' => 'A fully featured modern e-commerce platform.', 'ar' => 'منصة تجارة إلكترونية حديثة بكامل المميزات.'],
                'client' => 'Tech Corp',
                'url' => 'https://example.com',
                'tags' => ['Laravel', 'React', 'Tailwind'],
                'type' => 'portfolio',
                'image' => null,
            ],
            [
                'name' => ['en' => 'Health Care App', 'ar' => 'تطبيق رعاية صحية'],
                'category' => 'mobile',
                'description' => ['en' => 'Mobile application for booking doctors and clinics.', 'ar' => 'تطبيق جوال لحجز الأطباء والعيادات.'],
                'client' => 'Health Plus',
                'url' => 'https://example.com',
                'tags' => ['Flutter', 'Firebase'],
                'type' => 'portfolio',
                'image' => null,
            ],
            [
                'name' => ['en' => 'Real Estate Dashboard', 'ar' => 'لوحة تحكم عقارات'],
                'category' => 'websites',
                'description' => ['en' => 'Admin dashboard for managing properties and agents.', 'ar' => 'لوحة تحكم لإدارة العقارات والوكلاء.'],
                'client' => 'Real Estate Co',
                'url' => 'https://example.com',
                'tags' => ['Vue.js', 'Laravel'],
                'type' => 'portfolio',
                'image' => null,
            ],
            [
                'name' => ['en' => 'Corporate Branding', 'ar' => 'هوية بصرية لشركة'],
                'category' => 'digital',
                'description' => ['en' => 'Complete brand identity and visual guidelines.', 'ar' => 'هوية بصرية كاملة وتصميمات إرشادية.'],
                'client' => 'Creative Agency',
                'url' => 'https://example.com',
                'tags' => ['Illustrator', 'Photoshop'],
                'type' => 'portfolio',
                'image' => null,
            ],
            [
                'name' => ['en' => 'Food Delivery App', 'ar' => 'تطبيق توصيل طلبات'],
                'category' => 'mobile',
                'description' => ['en' => 'Food delivery application with real-time tracking.', 'ar' => 'تطبيق توصيل طلبات مع تتبع حي للطلبات.'],
                'client' => 'Foodie',
                'url' => 'https://example.com',
                'tags' => ['React Native', 'Node.js'],
                'type' => 'portfolio',
                'image' => null,
            ],
            [
                'name' => ['en' => 'Fitness Store', 'ar' => 'متجر أدوات رياضية'],
                'category' => 'websites',
                'description' => ['en' => 'Online store for selling high-quality fitness equipment.', 'ar' => 'متجر إلكتروني لبيع المعدات الرياضية عالية الجودة.'],
                'client' => 'FitLife',
                'url' => 'https://example.com',
                'tags' => ['Shopify', 'Liquid'],
                'type' => 'portfolio',
                'image' => null,
            ],
        ];

        foreach ($projects as $project) {
            Project::create($project);
        }
    }
}
