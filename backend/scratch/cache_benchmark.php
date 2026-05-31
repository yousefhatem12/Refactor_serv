<?php
$t1 = microtime(true);

// 1. Bootstrap Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$t2 = microtime(true);
echo "Laravel Bootstrap Time: " . round(($t2 - $t1) * 1000, 2) . " ms\n\n";

// --- Helper Benchmarking Function ---
function benchmark($label, $action, $iterations = 100) {
    $start = microtime(true);
    for ($i = 0; $i < $iterations; $i++) {
        $action();
    }
    $end = microtime(true);
    $totalTime = ($end - $start) * 1000;
    $avgTime = $totalTime / $iterations;
    echo sprintf("%-40s | Total: %8.2f ms | Avg: %6.4f ms\n", $label, $totalTime, $avgTime);
}

// Ensure cache is populated first
$lang = 'ar';
$cacheKeyProducts = "products_{$lang}_cat_all_page_1_per_6";
$cacheKeySettings = "settings_{$lang}";
$cacheKeyServices = "services_{$lang}";

// Warm up the caches
\Illuminate\Support\Facades\Cache::forget($cacheKeyProducts);
\Illuminate\Support\Facades\Cache::forget($cacheKeySettings);
\Illuminate\Support\Facades\Cache::forget($cacheKeyServices);

// Benchmark Database Queries
echo "=== DATABASE QUERIES ===\n";

benchmark("Eloquent Products Query (Page 1)", function() use ($lang) {
    $products = \App\Models\Product::where('is_active', true)->orderBy('id', 'asc')->take(6)->get()->toArray();
}, 50);

benchmark("Eloquent Settings Query", function() {
    $settings = \App\Models\Setting::all()->toArray();
}, 50);

benchmark("Eloquent Services Query", function() {
    $services = \App\Models\Service::where('is_active', true)->get()->toArray();
}, 50);

// Populate Cache
\Illuminate\Support\Facades\Cache::remember($cacheKeyProducts, 3600, function() {
    return \App\Models\Product::where('is_active', true)->orderBy('id', 'asc')->take(6)->get()->toArray();
});
\Illuminate\Support\Facades\Cache::remember($cacheKeySettings, 3600, function() {
    return \App\Models\Setting::all()->toArray();
});
\Illuminate\Support\Facades\Cache::remember($cacheKeyServices, 3600, function() {
    return \App\Models\Service::where('is_active', true)->get()->toArray();
});

echo "\n=== CACHED READS ===\n";

benchmark("Cached Products (Page 1)", function() use ($cacheKeyProducts) {
    $products = \Illuminate\Support\Facades\Cache::get($cacheKeyProducts);
}, 50);

benchmark("Cached Settings", function() use ($cacheKeySettings) {
    $settings = \Illuminate\Support\Facades\Cache::get($cacheKeySettings);
}, 50);

benchmark("Cached Services", function() use ($cacheKeyServices) {
    $services = \Illuminate\Support\Facades\Cache::get($cacheKeyServices);
}, 50);
