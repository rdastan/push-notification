<?php

namespace App\Jobs;

use App\Models\Device;
use App\Models\Notification;
use App\Models\NotificationLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Factory;
use Illuminate\Support\Facades\Log;

class SendPushNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    protected $notification;

    public function __construct(Notification $notification)
    {
        $this->notification = $notification;
    }

    public function handle()
    {
        try {
            $firebase = (new Factory)
                ->withServiceAccount(config('firebase.projects.app.credentials'))
                ->createMessaging();
        } catch (\Exception $e) {
            Log::error('Инициализация Firebase не удалась: ' . $e->getMessage());

            $devices = Device::all();

            foreach ($devices as $device) {
                NotificationLog::create([
                    'notification_id' => $this->notification->id,
                    'device_id' => $device->id,
                    'status' => 'failed',
                    'error_message' => 'Инициализация Firebase не удалась: ' . $e->getMessage(),
                ]);
            }

            return;
        }

        $devices = Device::all();

        if ($devices->isEmpty()) {
            Log::warning('Нет устройств для уведомления с ID: ' . $this->notification->id);

            return;
        }

        foreach ($devices as $device) {
            $log = NotificationLog::create([
                'notification_id' => $this->notification->id,
                'device_id' => $device->id,
                'status' => 'pending',
            ]);

            try {
                $message = CloudMessage::withTarget('token', $device->device_token)
                    ->withNotification([
                        'title' => 'Новое уведомление',
                        'body' => $this->notification->message,
                    ]);

                $firebase->send($message);

                $log->update([
                    'status' => 'sent',
                ]);
            } catch (\Exception $e) {
                $log->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                ]);
            }
        }
    }
}
