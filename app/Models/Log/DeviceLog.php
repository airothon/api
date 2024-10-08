<?php

namespace App\Models\Log;

use App\Observers\Log\DeviceLogObserver as Observer;
use MongoDB\Laravel\Eloquent\Model;

class DeviceLog extends Model
{
    protected $connection = 'log';

    protected $fillable = [
        'mac',
        'val1',
        'val2',
        'val3',
        'calculated_at',
    ];

    protected $dateFormat = 'Y-m-d H:i:s.u';


    public static function boot(): void
    {
        parent::boot();

        static::observe(Observer::class);
    }
}