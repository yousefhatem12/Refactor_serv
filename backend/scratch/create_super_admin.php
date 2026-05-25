<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

try {
    // 1. Create the roles if they don't exist
    $readOnlyRole = Role::firstOrCreate(['name' => 'read_only', 'guard_name' => 'web']);
    $role = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
    echo "Roles created/found.\n";

    // 2. Create the admin user
    $adminEmail = env('ADMIN_EMAIL', 'admin@serv5.com');
    $adminPassword = env('ADMIN_PASSWORD', 'admin123456');
    
    $user = User::where('email', $adminEmail)->first();
    if (!$user) {
        $user = User::create([
            'name' => 'Super Admin',
            'email' => $adminEmail,
            'password' => Hash::make($adminPassword),
        ]);
        echo "Admin user created successfully.\n";
    } else {
        $user->update([
            'password' => Hash::make($adminPassword),
        ]);
        echo "Admin user password updated successfully.\n";
    }

    // 3. Assign super_admin role to the user
    $user->syncRoles([$role]);
    echo "super_admin role assigned to user.\n";

    echo "\nCredentials:\nEmail: $adminEmail\nPassword: $adminPassword\n";

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
