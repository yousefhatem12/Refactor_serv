<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\PortfolioController;
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\FaqController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\PageController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CareerController;
use App\Http\Controllers\Api\ContactController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/login', function () {
    return response()->json(['message' => 'Unauthenticated.'], 401);
})->name('login');

// v1 Versioned API Group
Route::prefix('v1')->group(function () {
    
    // Auth endpoints
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });

    // Services
    Route::prefix('services')->group(function () {
        Route::get('/', [ServiceController::class, 'index'])->name('api.services.index');
        Route::get('/{slug}', [ServiceController::class, 'show'])->name('api.services.show');
    });

    // Portfolio
    Route::prefix('portfolio')->group(function () {
        Route::get('/', [PortfolioController::class, 'index'])->name('api.portfolio.index');
        Route::get('/categories', [PortfolioController::class, 'categories'])->name('api.portfolio.categories');
        Route::get('/{slug}', [PortfolioController::class, 'show'])->name('api.portfolio.show');
    });

    // Blog
    Route::prefix('blog')->group(function () {
        Route::get('/', [BlogController::class, 'index'])->name('api.blog.index');
        Route::get('/categories', [BlogController::class, 'categories'])->name('api.blog.categories');
        Route::get('/trending', [BlogController::class, 'trending'])->name('api.blog.trending');
        Route::get('/{slug}', [BlogController::class, 'show'])->name('api.blog.show');
    });

    // FAQs
    Route::get('/faqs', [FaqController::class, 'index'])->name('api.faqs.index');

    // Products
    Route::prefix('products')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('api.products.index');
        Route::get('/categories', [ProductController::class, 'categories'])->name('api.products.categories');
        Route::get('/{slug}', [ProductController::class, 'show'])->name('api.products.show');
    });

    // Careers
    Route::prefix('careers')->group(function () {
        Route::get('/', [CareerController::class, 'index'])->name('api.careers.index');
        Route::post('/apply', [CareerController::class, 'apply'])->name('api.careers.apply');
    });

    // Contact & Quotations
    Route::post('/contact', [ContactController::class, 'submitMessage'])->name('api.contact.submit');
    Route::post('/quotations', [ContactController::class, 'submitQuotation'])->name('api.quotations.submit');
    Route::post('/consultations', [ContactController::class, 'submitConsultation'])->name('api.consultations.submit');

    // Settings & System Data
    Route::get('/settings', [SettingController::class, 'settings'])->name('api.settings');
    Route::get('/testimonials', [SettingController::class, 'testimonials'])->name('api.testimonials');
    Route::get('/team', [SettingController::class, 'team'])->name('api.team');
    Route::get('/features', [SettingController::class, 'features'])->name('api.features');
    Route::get('/footer-data', [SettingController::class, 'footer'])->name('api.footer');
    Route::get('/nav-links', [SettingController::class, 'navLinks'])->name('api.nav-links');
    Route::get('/pricing-plans', [SettingController::class, 'pricingPlans'])->name('api.pricing-plans');
    Route::get('/bank-accounts', [SettingController::class, 'bankAccounts'])->name('api.bank-accounts');

    // Pages
    Route::get('/pages/{slug}', [PageController::class, 'show'])->name('api.pages.show');
});
