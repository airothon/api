<?php

namespace App\Models\System;

use App\Observers\System\DeviceObserver as Observer;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $connection = 'system';

    protected $fillable = [
        'user_id',
        'mac',
        'connected_at',
        'last_communicated_at',
    ];

    protected $casts = [
        'connected_at' => 'datetime',
        'last_communicated_at' => 'datetime',
    ];

    protected $dateFormat = 'Y-m-d H:i:s.u';

    public function logs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\Log\DeviceLog::class, 'mac', 'mac');
    }


    public static function boot(): void
    {
        parent::boot();

        static::observe(Observer::class);
    }
}
