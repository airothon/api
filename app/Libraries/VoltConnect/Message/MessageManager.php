<?php

namespace App\Libraries\VoltConnect\Message;

use App\Libraries\VoltConnect\VoltConnect;
use App\Models\System\Device;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;

class MessageManager
{
    protected VoltConnect $airothon;

    protected array $items = [
        FC::class,
        HEARTBEAT::class,
        MESSAGE::class,
        RS::class,
        START::class,
        TS::class,
    ];

    protected array $messages = [];


    public function __construct($airothon)
    {
        $this->airothon = $airothon;

        foreach ($this->items as $item) {
            $message = new $item($this->airothon);
            $this->messages[$message->getCommand()] = $message;
        }
    }

    public function get(string $signature)
    {
        return $this->messages[$signature];
    }

    public function has(string $signature): bool
    {
        return array_key_exists($signature, $this->messages);
    }

    public function all(): array
    {
        return $this->messages;
    }

    public function parse(Stringable|string $message, string $ip): Device|string
    {
        if (!$message instanceof Stringable) {
            $message = Str::of($message);
        }
        $message = $message->trim('#,$');

        if (!$this->has($message->before('|'))) {
            throw new \Exception('Undefined command');
        }

        return $this->get($message->before('|'))->handle(Str::of($message->after('|')), $ip);
    }

    public function getSerialNumber(Stringable $message): null|string
    {
        $message = $message->explode('|');

        if (count($message) > 1) {
            return $message[1];
        }

        return null;
    }
}
