<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\NavLink;

$links = NavLink::orderBy('sort_order')->get();
foreach ($links as $link) {
    echo "ID: {$link->id} | Label: " . json_encode($link->label, JSON_UNESCAPED_UNICODE) . " | URL: {$link->url} | Parent ID: {$link->parent_id} | Sort Order: {$link->sort_order}\n";
}
