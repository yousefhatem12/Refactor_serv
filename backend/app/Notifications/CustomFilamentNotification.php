<?php

namespace App\Notifications;

use Filament\Notifications\Notification as FilamentNotification;

class CustomFilamentNotification extends FilamentNotification
{
    public function toDatabase(): SyncDatabaseNotification
    {
        return new SyncDatabaseNotification($this->getDatabaseMessage());
    }
}
