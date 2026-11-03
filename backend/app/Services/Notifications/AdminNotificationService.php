<?php

namespace App\Services\Notifications;

use App\Contracts\Notifications\AdminNotificationServiceInterface;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Cache;

class AdminNotificationService implements AdminNotificationServiceInterface
{
    public function notifySuperAdminsAfterResponse(
        string $title,
        string $body,
        string $icon,
        ?string $viewUrl = null
    ): void {
        $admins = Cache::remember('super_admins_notify', 300, static fn () => User::role('super_admin')->get());

        app()->terminating(static function () use ($admins, $title, $body, $icon, $viewUrl): void {
            $notification = Notification::make()
                ->title($title)
                ->body($body)
                ->icon($icon)
                ->success();

            if ($viewUrl !== null) {
                $actionClass = 'Filament\\Actions\\Action';
                $notification->actions([
                    $actionClass::make('view')
                        ->label('View')
                        ->link()
                        ->url(static fn () => $viewUrl)
                        ->markAsRead()
                        ->extraAttributes([
                            'x-on:click' => '$dispatch(\'close-database-notifications\')',
                        ]),
                ]);
            }

            $notification->sendToDatabase($admins);
        });
    }
}
