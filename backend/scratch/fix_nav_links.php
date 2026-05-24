<?php
// Fix nav_links that have wrong service URLs (missing /services/ prefix)
// e.g. /services-web-development -> /services/services-web-development

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\NavLink;

$fixed = 0;
$links = NavLink::all();

foreach ($links as $link) {
    if ($link->url && preg_match('/^\/services-/', $link->url) && !str_starts_with($link->url, '/services/')) {
        $old = $link->url;
        $link->url = '/services' . $link->url;
        $link->save();
        echo "Fixed: {$old} -> {$link->url}\n";
        $fixed++;
    }
}

echo "\nTotal fixed: {$fixed}\n";
