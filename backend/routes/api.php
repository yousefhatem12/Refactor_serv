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

// Public endpoints
Route::get('/services', [ApiController::class, 'getServices']);
Route::get('/portfolio', [ApiController::class, 'getProjects']);
Route::get('/blog', [ApiController::class, 'getPosts']);
Route::get('/blog/{slug}', [ApiController::class, 'getPost']);
Route::get('/trending-posts', [ApiController::class, 'getTrendingPosts']);
Route::get('/faqs', [ApiController::class, 'getFaqs']);
Route::get('/settings', [ApiController::class, 'getSettings']);
Route::get('/testimonials', [ApiController::class, 'getTestimonials']);
Route::get('/team', [ApiController::class, 'getTeamMembers']);
Route::get('/features', [ApiController::class, 'getFeatures']);
Route::get('/statistics', [ApiController::class, 'getStatistics']);
Route::get('/footer-data', [ApiController::class, 'getFooterData']);
Route::get('/nav-links', [ApiController::class, 'getNavLinks']);
Route::get('/pricing-plans', [ApiController::class, 'getPricingPlans']);
Route::get('/pages/{slug}', [ApiController::class, 'getPageContent']);

// Protected endpoints
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/contact', [ApiController::class, 'submitContactMessage']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
});
