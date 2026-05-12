<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$admins = \App\Models\User::role('super_admin')->get();
echo "Super Admins count: " . $admins->count() . "\n";
foreach ($admins as $admin) {
    echo "Admin ID: " . $admin->id . ", Email: " . $admin->email . "\n";
}
