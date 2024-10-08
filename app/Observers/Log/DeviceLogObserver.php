<?php

namespace App\Observers\Log;

use App\Models\Log\DeviceLog as Model;

class DeviceLogObserver
{
    public function creating(Model $model): void
    {
    }
}
