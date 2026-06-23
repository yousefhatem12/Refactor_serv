<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$service = \App\Models\Service::find(1);
if ($service) {
    echo "SERVICE NAME: " . json_encode($service->name) . "\n";
    echo "SECTIONS DATA:\n";
    echo json_encode($service->sections, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
} else {
    echo "Service ID 1 not found.\n";
}
