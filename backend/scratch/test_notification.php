<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $admins = \App\Models\User::role('super_admin')->get();
    if ($admins->count() > 0) {
        \Filament\Notifications\Notification::make()
            ->title('Test')
            ->body('Hello Admin')
            ->sendToDatabase($admins);
        echo "Sent to " . $admins->count() . " admins.\n";
    } else {
        echo "No admins found.\n";
    }
    echo "Total Notifications in DB: " . \Illuminate\Support\Facades\DB::table('notifications')->count() . "\n";
} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n" . $e->getTraceAsString();
}
