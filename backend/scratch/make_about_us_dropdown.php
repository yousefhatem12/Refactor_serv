<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\NavLink;

// 1. Find the parent link for About Us
$parent = NavLink::where('url', '/about-us')->whereNull('parent_id')->first();
if (!$parent) {
    // If not found by url, try by label or create it
    $parent = NavLink::where('label->en', 'About us')->whereNull('parent_id')->first();
}

if ($parent) {
    // Update parent URL to '#' so it acts as a dropdown trigger
    $parent->update(['url' => '#']);
    echo "Updated parent NavLink ID {$parent->id} URL to '#'.\n";
    
    // 2. Create the child link pointing to '/about-us'
    $aboutChild = NavLink::where('parent_id', $parent->id)->where('url', '/about-us')->first();
    if (!$aboutChild) {
        NavLink::create([
            'parent_id' => $parent->id,
            'label' => $parent->label, // Reuse parent's translations
            'url' => '/about-us',
            'sort_order' => 1,
            'is_active' => true,
        ]);
        echo "Created child link for About Us pointing to /about-us.\n";
    } else {
        echo "Child link for /about-us already exists.\n";
    }

    // 3. Create the child link pointing to '/bank-accounts'
    $bankChild = NavLink::where('parent_id', $parent->id)->where('url', '/bank-accounts')->first();
    if (!$bankChild) {
        NavLink::create([
            'parent_id' => $parent->id,
            'label' => ['en' => 'Bank Accounts', 'ar' => 'الحسابات البنكية'],
            'url' => '/bank-accounts',
            'sort_order' => 2,
            'is_active' => true,
        ]);
        echo "Created child link for Bank Accounts pointing to /bank-accounts.\n";
    } else {
        echo "Child link for /bank-accounts already exists.\n";
    }
} else {
    echo "Parent NavLink for About Us not found.\n";
}
