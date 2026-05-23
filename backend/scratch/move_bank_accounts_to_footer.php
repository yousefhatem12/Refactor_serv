<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\NavLink;
use App\Models\FooterLink;

// 1. Delete NavLink
$deleted = NavLink::where('url', '/bank-accounts')->delete();
echo "Deleted " . $deleted . " NavLink(s) for /bank-accounts.\n";

// 2. Add FooterLink
$exists = FooterLink::where('url', '/bank-accounts')->first();
if (!$exists) {
    // Find the next sort order for useful_links
    $maxSort = FooterLink::where('column_name', 'useful_links')->max('sort_order') ?? 0;
    FooterLink::create([
        'column_name' => 'useful_links',
        'label' => ['en' => 'Bank Accounts', 'ar' => 'الحسابات البنكية'],
        'url' => '/bank-accounts',
        'sort_order' => $maxSort + 1,
    ]);
    echo "Added /bank-accounts to FooterLink (useful_links) successfully!\n";
} else {
    echo "FooterLink for /bank-accounts already exists.\n";
}
