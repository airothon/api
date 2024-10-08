<?php

namespace App\Libraries\VoltConnect;

use App\Libraries\VoltConnect\Message\MessageManager;

class VoltConnect
{
    protected MessageManager $messageManager;

    public function __construct()
    {
        $this->messageManager = new MessageManager($this);
    }

    public function message(): MessageManager
    {
        return $this->messageManager;
    }
}
