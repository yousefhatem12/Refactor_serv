<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Service;

$services = Service::all();
foreach ($services as $s) {
    echo "ID: {$s->id}, Slug: {$s->slug}\n";
}
