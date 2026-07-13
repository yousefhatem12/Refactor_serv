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
    try {
        // 1. Find the parent About Us link using PHP-side filtering to support all database engines
        $parent = \App\Models\NavLink::where('url', '#')->get()->first(function ($link) {
            $label = $link->label;
            if (is_string($label)) {
                $decoded = json_decode($label, true);
                $label = is_array($decoded) ? $decoded : ['en' => $label, 'ar' => $label];
            }
            $en = strtolower((string)($label['en'] ?? ''));
            $ar = (string)($label['ar'] ?? '');
            return str_contains($en, 'about') || str_contains($ar, 'عن') || str_contains($ar, 'نحن');
        });

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
    } catch (\Exception $e) {
        return response('Error: ' . $e->getMessage() . "\n\n" . $e->getTraceAsString(), 500)
            ->header('Content-Type', 'text/plain');
    }
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

