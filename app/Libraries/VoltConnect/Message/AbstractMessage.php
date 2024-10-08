<?php

namespace App\Libraries\VoltConnect\Message;

use App\Libraries\VoltConnect\VoltConnect;
use Illuminate\Support\Stringable;

class AbstractMessage
{
    protected string $command;
    protected  string $description;
    protected VoltConnect $airothon;

    public function __construct($airothon)
    {
        $this->airothon = $airothon;
    }

    public function getCommand(): string
    {
        return $this->command;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function handle(Stringable $message, string $ip)
    {

    }
}
