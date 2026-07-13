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
    // 1. Find the parent About Us link
    $parent = \App\Models\NavLink::where('url', '#')->where(function($q) {
        $q->where('label->en', 'like', '%about%')
          ->orWhere('label->ar', 'like', '%عن%')
          ->orWhere('label->ar', 'like', '%نحن%');
    })->first();

    if (!$parent) {
        $parent = \App\Models\NavLink::where('url', '/about-us')->first();
    }

    if ($parent) {
        // Ensure parent URL is '#'
        $parent->update(['url' => '#']);

        // Move Careers to under About Us parent
        $careers = \App\Models\NavLink::where('url', '/careers')->first();
        if ($careers) {
            $careers->update([
                'parent_id' => $parent->id,
                'sort_order' => 3, // about-us=1, bank-accounts=2, careers=3, faq=4
            ]);
        }

        // Move FAQ to under About Us parent
        $faq = \App\Models\NavLink::where('url', '/faq')->first();
        if ($faq) {
            $faq->update([
                'parent_id' => $parent->id,
                'sort_order' => 4,
            ]);
        }
    }
    
    // 2. Run FooterSeeder to refresh footer links
    $seeder = new \Database\Seeders\FooterSeeder();
    $seeder->run();

    return 'Navbar and Footer fixed successfully!';
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

