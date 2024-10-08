<?php

use App\Models\System\Device;

if (!function_exists('telnet')) {
    function telnet(Device $device, $message)
    {
        cache()->driver('redis')->set('telnet:server:device.' . $device->id . ':sendMessage' . $device->id, $message);
    }
}
