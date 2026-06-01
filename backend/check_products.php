<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;
$products = Product::all(['slug', 'image', 'samples'])->toArray();
echo json_encode($products, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
