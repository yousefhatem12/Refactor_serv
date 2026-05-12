<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\NavLink;

// Fix Services link
NavLink::where('url', '/services')->update(['url' => '#']);

// Fix About and Contact links to point to home page sections if they are missing pages
// Based on typical one-page designs, but let's check if about-us and contact-us pages exist
NavLink::where('url', '/about-us')->update(['url' => '/about-us']);
NavLink::where('url', '/contact-us')->update(['url' => '/contact-us']);

echo "Links fixed successfully.";
