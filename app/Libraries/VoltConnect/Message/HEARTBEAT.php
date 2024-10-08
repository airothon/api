<?php

namespace App\Libraries\VoltConnect\Message;

use Illuminate\Support\Stringable;

class HEARTBEAT extends AbstractMessage
{
    protected string $command = 'HEARTBEAT';
    protected string $description = 'Haberleşme ünitesi ilk bağlantı';

    public function handle(Stringable $message, string $ip)
    {
        return '';
    }
}
