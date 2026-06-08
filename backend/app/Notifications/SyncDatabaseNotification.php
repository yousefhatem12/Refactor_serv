<?php

namespace App\Notifications;

use Filament\Notifications\DatabaseNotification as FilamentDatabaseNotification;

class SyncDatabaseNotification extends FilamentDatabaseNotification
{
    public $connection = 'sync';
}
