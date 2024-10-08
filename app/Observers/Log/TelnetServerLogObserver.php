<?php

namespace App\Observers\Log;

use App\Models\Log\TelnetServerLog as Model;

class TelnetServerLogObserver
{
    public function creating(Model $model): void
    {
    }
}
