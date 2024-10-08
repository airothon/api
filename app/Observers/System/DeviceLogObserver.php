<?php

namespace App\Observers\System;

use App\Models\Log\DeviceLog as Model;

class DeviceLogObserver
{
    public function creating(Model $model): void
    {
        if (empty($model->direction)) {
            $model->direction = 0;
        }
    }
}
