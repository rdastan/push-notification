<?php

namespace App\Http\Controllers;

use App\Http\Requests\Device\CreateRequest;
use App\Models\Device;

class DeviceController extends Controller
{
    public function register(CreateRequest $request)
    {
        $device = Device::updateOrCreate(
            [
                'device_token' => $request->device_token,
                'user_id' => $request->user_id,
            ],
            [
                'device_token' => $request->device_token,
                'user_id' => $request->user_id,
            ]
        );

        return response()->json([
            'message' => 'Устройство успешно зарегистрировано',
            'device' => $device
        ], 200);
    }
}
