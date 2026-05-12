<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\NavLink;

$links = NavLink::all();
foreach ($links as $l) {
    echo "ID: {$l->id}, Label: " . json_encode($l->label) . ", URL: {$l->url}, Parent: " . ($l->parent_id ?? 'None') . "\n";
}
