<?php
$t1 = microtime(true);

// 1. Load autoload and bootstrap Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$t2 = microtime(true);
echo "Laravel Bootstrap Time: " . (($t2 - $t1) * 1000) . " ms\n";

// 2. Measure simple Eloquent query
$t3 = microtime(true);
$projects = \App\Models\Project::take(9)->get();
$t4 = microtime(true);
echo "Eloquent Query (9 projects) Time: " . (($t4 - $t3) * 1000) . " ms\n";

// 3. Measure Cache Read
$t5 = microtime(true);
$cache = \Illuminate\Support\Facades\Cache::get('test_key');
$t6 = microtime(true);
echo "Cache Get Time: " . (($t6 - $t5) * 1000) . " ms\n";
