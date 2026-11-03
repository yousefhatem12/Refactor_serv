<?php

namespace App\Contracts\Notifications;

interface AdminNotificationServiceInterface
{
    public function notifySuperAdminsAfterResponse(
        string $title,
        string $body,
        string $icon,
        ?string $viewUrl = null
    ): void;
}
