<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/fix-nav', function () {
    // 1. Update/Create FAQ in Navbar
    \App\Models\NavLink::updateOrCreate(
        ['url' => '/faq'], 
        ['label' => ['en' => 'FAQ', 'ar' => 'الأسئلة الشائعة'], 'sort_order' => 6, 'is_active' => true]
    );
    
    // 2. Run FooterSeeder to refresh footer links
    $seeder = new \Database\Seeders\FooterSeeder();
    $seeder->run();

    return 'Navbar and Footer fixed successfully!';
});
