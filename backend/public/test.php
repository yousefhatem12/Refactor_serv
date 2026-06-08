<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$page = \App\Models\Page::where('slug', 'about-us')->first();
$data = $page ? $page->sections : null;

file_put_contents(__DIR__.'/dump.txt', json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
echo "Done!";
