<?php
$t1 = microtime(true);

// 1. Bootstrap Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$t2 = microtime(true);
echo "Laravel Bootstrap: " . round(($t2 - $t1) * 1000, 2) . " ms\n\n";

// Clear cache to measure raw database / logic execution time
\Illuminate\Support\Facades\Cache::flush();
echo "Cache cleared for raw database measurement.\n\n";

$controller = $app->make(\App\Http\Controllers\Api\ApiController::class);

// Helper to benchmark controller action
function timeAction($label, $action) {
    $start = microtime(true);
    try {
        $response = $action();
        $end = microtime(true);
        $content = json_decode($response->getContent(), true);
        $count = is_array($content) ? count($content) : 1;
        echo sprintf("%-40s | Time: %8.2f ms | Items: %d\n", $label, ($end - $start) * 1000, $count);
    } catch (\Exception $e) {
        $end = microtime(true);
        echo sprintf("%-40s | FAILED after %8.2f ms | Error: %s\n", $label, ($end - $start) * 1000, $e->getMessage());
    }
}

// Mock Request class
$request = new \Illuminate\Http\Request();

timeAction("getPageContent('home')", function() use ($controller) {
    return $controller->getPageContent('home');
});

timeAction("getFeatures()", function() use ($controller) {
    return $controller->getFeatures();
});

timeAction("getSettings()", function() use ($controller) {
    return $controller->getSettings();
});

timeAction("getServices()", function() use ($controller) {
    return $controller->getServices();
});

timeAction("getProducts()", function() use ($controller, $request) {
    return $controller->getProducts($request);
});

timeAction("getProductCategories()", function() use ($controller) {
    return $controller->getProductCategories();
});

timeAction("getBlogCategories()", function() use ($controller) {
    return $controller->getBlogCategories();
});

timeAction("getTrendingPosts()", function() use ($controller) {
    return $controller->getTrendingPosts();
});

timeAction("getTestimonials()", function() use ($controller) {
    return $controller->getTestimonials();
});

timeAction("getBankAccounts()", function() use ($controller) {
    return $controller->getBankAccounts();
});
