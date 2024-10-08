<?php

namespace App\Libraries\VoltConnect\Message;

use App\Models\System\Device;

class START extends AbstractMessage
{
    protected string $command = 'START';
    protected string $description = 'Haberleşme ünitesi zaman senkronizasyonu';

    public function send(Device $device, string $hash = null)
    {
        telnet($device, $this->context());
    }

    public function context()
    {
        return '#' . $this->getCommand() . '|1234$';
    }
}
