<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;
use App\Models\User;

try {
    $admins = User::role('super_admin')->get();
    Notification::make()
        ->title('Action Test')
        ->body('Testing clickable notification')
        ->actions([
            Action::make('view')
                ->label('View')
                ->url('http://localhost/admin/contact-messages')
                ->markAsRead(),
        ])
        ->sendToDatabase($admins);
    echo "Sent successfully.\n";
} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
echo "Total Count: " . \Illuminate\Support\Facades\DB::table('notifications')->count() . "\n";
