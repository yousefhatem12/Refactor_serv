<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$pages = \App\Models\Page::all(['title', 'slug'])->map(function($p) {
    return $p->slug;
})->toArray();

$projects = \App\Models\Project::all(['name', 'id'])->map(function($p) {
    return $p->id;
})->toArray();

echo "PAGES SLUGS:\n" . implode("\n", $pages) . "\n\n";
echo "PORTFOLIO IDS:\n" . implode("\n", $projects) . "\n\n";
echo "SERVICES COUNT: " . \App\Models\Service::count() . "\n";
echo "PRODUCTS COUNT: " . \App\Models\Product::count() . "\n";
echo "BLOGS COUNT: " . \App\Models\Post::count() . "\n";
