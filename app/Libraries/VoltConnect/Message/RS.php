<?php

namespace App\Libraries\VoltConnect\Message;

use App\Models\System\Device;

class RS extends AbstractMessage
{
    protected string $command = 'RS';
    protected string $description = 'Haberleşme ünitesini yeniden başlatma';

    public function send(Device $device)
    {
        telnet($device, $this->context());
    }

    public function context()
    {
        return '#' . $this->getCommand() . '$';
    }
}
