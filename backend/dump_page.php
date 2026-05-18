<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
$page = \App\Models\Page::where('slug', 'home')->first();
echo json_encode([
    'title' => $page->title,
    'subtitle' => $page->subtitle,
    'content' => $page->content,
    'hero_image' => $page->hero_image,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
