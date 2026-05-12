<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$latest = \Illuminate\Support\Facades\DB::table('notifications')->latest()->first();
if ($latest) {
    echo "Latest Notification Created At: " . $latest->created_at . "\n";
    echo "Data: " . $latest->data . "\n";
} else {
    echo "No notifications found.\n";
}
echo "Total Count: " . \Illuminate\Support\Facades\DB::table('notifications')->count() . "\n";
