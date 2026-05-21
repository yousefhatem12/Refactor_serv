<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\NavLink;

$exists = NavLink::where('url', '/bank-accounts')->first();
if (!$exists) {
    $contactUs = NavLink::where('url', '/contact-us')->first();
    $sortOrder = 12;
    if ($contactUs) {
        $sortOrder = $contactUs->sort_order;
        NavLink::where('sort_order', '>=', $sortOrder)->increment('sort_order');
    }
    
    NavLink::create([
        'label' => ['en' => 'Bank Accounts', 'ar' => 'الحسابات البنكية'],
        'url' => '/bank-accounts',
        'sort_order' => $sortOrder,
        'is_active' => true,
    ]);
    echo "Added Bank Accounts navigation link successfully!\n";
} else {
    echo "Bank Accounts navigation link already exists.\n";
}
