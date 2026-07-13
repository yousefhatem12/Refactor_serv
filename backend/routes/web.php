<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/ar', function () {
    return view('welcome');
});

Route::get('/en', function () {
    return view('welcome');
});

Route::get('/fix-nav', function () {
    // 1. Update/Create FAQ in Navbar
    \App\Models\NavLink::updateOrCreate(
        ['url' => '/faq'], 
        ['label' => ['en' => 'FAQ', 'ar' => 'الأسئلة الشائعة'], 'sort_order' => 4, 'is_active' => true]
    );

    // 2. Associate Careers and FAQ with About Us parent
    $aboutUs = \App\Models\NavLink::where('url', '/about-us')->first();
    if ($aboutUs) {
        \App\Models\NavLink::where('url', '/careers')->update(['parent_id' => $aboutUs->id, 'sort_order' => 3]);
        \App\Models\NavLink::where('url', '/faq')->update(['parent_id' => $aboutUs->id, 'sort_order' => 4]);
    }
    
    // 3. Run FooterSeeder to refresh footer links
    $seeder = new \Database\Seeders\FooterSeeder();
    $seeder->run();

    // 4. Flush application cache to clear any stale API responses
    \Illuminate\Support\Facades\Cache::flush();

    return 'Navbar, Footer, and Cache cleared successfully!';
});

Route::get('/temp-login', function () {
    $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'super_admin']);
    
    $user = \App\Models\User::whereHas('roles', function ($q) {
        $q->where('name', 'super_admin');
    })->first();
    
    if (!$user) {
        $user = \App\Models\User::first();
        if (!$user) {
            $user = \App\Models\User::create([
                'name' => 'Super Admin',
                'email' => 'admin@admin.com',
                'password' => bcrypt('password'),
            ]);
        }
        $user->assignRole($role);
    }
    
    \Illuminate\Support\Facades\Auth::login($user);
    return redirect('/admin');
});

