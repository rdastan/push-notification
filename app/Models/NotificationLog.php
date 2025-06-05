<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationLog extends Model
{
    protected $fillable = [
        'notification_id',
        'device_id',
        'status',
        'error_message',
    ];

    public function notification()
    {
        return $this->belongsTo(Notification::class);
    }

    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}
