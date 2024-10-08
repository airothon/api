<?php

namespace App\Observers\System;

use App\Models\System\Device as Model;

class DeviceObserver
{
    public function saving(Model $model): void
    {
        if ($model->ip == '10.0.2.2') {
            $model->ip = '192.168.0.9';
        }
    }
}
