<?php

namespace App\Libraries\VoltConnect\Message;

use App\Models\System\Device;

class MESSAGE extends AbstractMessage
{
    protected string $command = 'MESSAGE';
    protected string $description = 'Haberleşme ünitesi desteklenen mesaj tipleri';

    public function send(Device $device)
    {
        telnet($device, $this->context());
    }

    public function context()
    {
        return '#' . $this->getCommand() . '$';
    }
}
