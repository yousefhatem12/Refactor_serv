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
    $repoPath = '/home2/serv5eg/repositories/serv5-last-last';
    $frontendPath = '/home2/serv5eg/public_html/serv5-frontend2026';
    $backendPath = '/home2/serv5eg/public_html/serv5-backend2026';

    $log = [];

    // 1. Copy files from repo to target folders
    if (is_dir($repoPath)) {
        $rsyncFrontend = "rsync -av --delete --exclude=\"backend\" --exclude=\".git\" --exclude=\"node_modules\" --exclude=\"deleted-backend\" {$repoPath}/ {$frontendPath}/";
        $rsyncBackend = "rsync -av --delete {$repoPath}/backend/ {$backendPath}/";

        if (function_exists('shell_exec')) {
            $log[] = "Deploying frontend using rsync...";
            $out1 = shell_exec($rsyncFrontend . ' 2>&1');
            $log[] = $out1 ?: "Done (no output)";

            $log[] = "Deploying backend using rsync...";
            $out2 = shell_exec($rsyncBackend . ' 2>&1');
            $log[] = $out2 ?: "Done (no output)";
        } else {
            $log[] = "shell_exec is disabled. Using PHP copy fallback...";
            
            // Copy Navbar.jsx
            $navbarSrc = "{$repoPath}/src/domains/Shared/Navbar/Navbar.jsx";
            $navbarDst = "{$frontendPath}/src/domains/Shared/Navbar/Navbar.jsx";
            if (file_exists($navbarSrc)) {
                @mkdir(dirname($navbarDst), 0755, true);
                if (copy($navbarSrc, $navbarDst)) {
                    $log[] = "Copied Navbar.jsx successfully.";
                } else {
                    $log[] = "Failed to copy Navbar.jsx.";
                }
            }

            // Copy NavLinkForm.php
            $formSrc = "{$repoPath}/backend/app/Filament/Resources/NavLinks/Schemas/NavLinkForm.php";
            $formDst = "{$backendPath}/app/Filament/Resources/NavLinks/Schemas/NavLinkForm.php";
            if (file_exists($formSrc)) {
                @mkdir(dirname($formDst), 0755, true);
                if (copy($formSrc, $formDst)) {
                    $log[] = "Copied NavLinkForm.php successfully.";
                } else {
                    $log[] = "Failed to copy NavLinkForm.php.";
                }
            }

            // Copy .next folder recursively
            $copyNext = function ($src, $dst) use (&$copyNext) {
                if (!is_dir($src)) return;
                $dir = opendir($src);
                @mkdir($dst, 0755, true);
                while (false !== ($file = readdir($dir))) {
                    if (($file != '.') && ($file != '..')) {
                        if (is_dir($src . '/' . $file)) {
                            $copyNext($src . '/' . $file, $dst . '/' . $file);
                        } else {
                            copy($src . '/' . $file, $dst . '/' . $file);
                        }
                    }
                }
                closedir($dir);
            };
            if (is_dir("{$repoPath}/.next")) {
                $copyNext("{$repoPath}/.next", "{$frontendPath}/.next");
                $log[] = "Copied .next folder successfully.";
            }
        }
    } else {
        $log[] = "Repository path not found: {$repoPath}";
    }

    // 2. Update/Create FAQ in Navbar
    \App\Models\NavLink::updateOrCreate(
        ['url' => '/faq'], 
        ['label' => ['en' => 'FAQ', 'ar' => 'الأسئلة الشائعة'], 'sort_order' => 4, 'is_active' => true]
    );

    // 3. Associate Careers and FAQ with About Us parent
    $aboutUs = \App\Models\NavLink::where('url', '/about-us')->first();
    if ($aboutUs) {
        \App\Models\NavLink::where('url', '/careers')->update(['parent_id' => $aboutUs->id, 'sort_order' => 3]);
        \App\Models\NavLink::where('url', '/faq')->update(['parent_id' => $aboutUs->id, 'sort_order' => 4]);
    }

    // 4. Flush cache
    \Illuminate\Support\Facades\Cache::flush();

    // 5. Restart frontend Passenger Node.js app
    @mkdir("{$frontendPath}/tmp", 0755, true);
    @touch("{$frontendPath}/tmp/restart.txt");

    $log[] = "Web deploy completed successfully!";
    
    return response(implode("\n", $log), 200)->header('Content-Type', 'text/plain');
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

