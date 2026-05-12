<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\NavLink;

// Find Services parent
$services = NavLink::where('label->en', 'Services')->first();

if ($services) {
    // Ensure URL is # for dropdown to work
    $services->update(['url' => '#']);

    // Remove existing children if any to avoid duplicates
    NavLink::where('parent_id', $services->id)->delete();

    // Add children
    $children = [
        ['label' => ['en' => 'Web Development', 'ar' => 'برمجة المواقع'], 'url' => '/services-web-dev', 'sort_order' => 1],
        ['label' => ['en' => 'Web Design', 'ar' => 'تصميم المواقع'], 'url' => '/services-web-design', 'sort_order' => 2],
        ['label' => ['en' => 'Social Media', 'ar' => 'سوشيال ميديا'], 'url' => '/services-social-media', 'sort_order' => 3],
        ['label' => ['en' => 'Web Hosting', 'ar' => 'استضافة المواقع'], 'url' => '/services-web-hosting', 'sort_order' => 4],
    ];

    foreach ($children as $child) {
        NavLink::create(array_merge($child, [
            'parent_id' => $services->id,
            'is_active' => true,
        ]));
    }
    echo "Services dropdown restored successfully.";
} else {
    echo "Services link not found.";
}
