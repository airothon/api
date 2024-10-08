<?php

namespace App\Libraries\VoltConnect\Message;

use App\Models\System\Device;

class TS extends AbstractMessage
{
    protected string $command = 'TS';
    protected string $description = 'Haberleşme ünitesi zaman senkronizasyonu';

    public function send(Device $device)
    {
        telnet($device, $this->context());
    }

    public function context(): string
    {
        return '#' . $this->getCommand() . '|' . date('Y-m-d H:i:s') . '$';
    }
}
