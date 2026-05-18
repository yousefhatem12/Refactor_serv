<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Service;
$services = Service::all(['name', 'slug'])->toArray();
echo json_encode($services, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
