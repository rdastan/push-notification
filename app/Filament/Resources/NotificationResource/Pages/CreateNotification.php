<?php

namespace App\Filament\Resources\NotificationResource\Pages;

use App\Filament\Resources\NotificationResource;
use App\Jobs\SendPushNotification;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Notification;

class CreateNotification extends CreateRecord
{
    protected static string $resource = NotificationResource::class;

    protected function afterCreate(): void
    {
        $notification = $this->record;
        if ($notification->scheduled_at->isFuture()) {
            SendPushNotification::dispatch($notification)->delay($notification->scheduled_at);
        } else {
            SendPushNotification::dispatch($notification);
        }
    }
}
