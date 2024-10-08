<?php

namespace App\Models\Log;

use MongoDB\Laravel\Eloquent\Model;

class HttpLog extends Model
{
    protected $connection = 'log';

    protected $fillable = [
        'method',
        'uri',
        'headers',
        'body',
        'response',
        'response_code',
        'ip_address',
        'elapsed_time',
    ];
}
