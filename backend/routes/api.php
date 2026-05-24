<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\AuthController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Auth endpoints
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Temporary seeding endpoint
Route::get('/run-seeders', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('db:seed', ['--class' => 'FullEnglishSeeder']);
        return response()->json([
            'status' => 'success',
            'output' => \Illuminate\Support\Facades\Artisan::output()
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
});

// Public endpoints
Route::get('/services', [ApiController::class, 'getServices']);
Route::get('/services/{slug}', [ApiController::class, 'getService']);
Route::get('/portfolio', [ApiController::class, 'getProjects']);
Route::get('/portfolio-categories', [ApiController::class, 'getProjectCategories']);
Route::get('/blog', [ApiController::class, 'getPosts']);
Route::get('/blog/{slug}', [ApiController::class, 'getPost']);
Route::get('/trending-posts', [ApiController::class, 'getTrendingPosts']);
Route::get('/faqs', [ApiController::class, 'getFaqs']);
Route::get('/settings', [ApiController::class, 'getSettings']);
Route::get('/testimonials', [ApiController::class, 'getTestimonials']);
Route::get('/team', [ApiController::class, 'getTeamMembers']);
Route::get('/features', [ApiController::class, 'getFeatures']);
Route::get('/footer-data', [ApiController::class, 'getFooterData']);
Route::get('/nav-links', [ApiController::class, 'getNavLinks']);
Route::get('/pricing-plans', [ApiController::class, 'getPricingPlans']);
Route::get('/pages/{slug}', [ApiController::class, 'getPageContent']);
Route::get('/products', [ApiController::class, 'getProducts']);
Route::get('/products/{slug}', [ApiController::class, 'getProduct']);
Route::get('/careers', [ApiController::class, 'getCareers']);
Route::post('/careers/apply', [ApiController::class, 'submitCareerApplication']);
Route::get('/bank-accounts', [ApiController::class, 'getBankAccounts']);

// Protected endpoints
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/contact', [ApiController::class, 'submitContactMessage']);
    Route::post('/quotations', [ApiController::class, 'submitQuotation']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
});
