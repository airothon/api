<?php

namespace App\Models\Log;

use App\Models\User;
use MongoDB\Laravel\Eloquent\Model;

class UserDailyCalculated extends Model
{
    protected $connection = 'log';

    protected $fillable = [
        'user_id',
        'value',
        'token_minted_at',
    ];

    protected $dateFormat = 'Y-m-d H:i:s.u';

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
