<?php

namespace App\Libraries\VoltConnect\Message;

use App\Models\System\Device as Model;
use Illuminate\Support\Stringable;

class FC extends AbstractMessage
{
    protected string $command = 'FC';
    protected string $description = 'Haberleşme ünitesi ilk bağlantı';

    public function handle(Stringable $message, string $ip)
    {
        $message = $message->explode('|');
        if (count($message) < 9) {
            throw new \Exception('Eksik param');
        }

        $model = Model::where(['serial_number' => $message[0]])->first();
        if (!$model) {
            $model = new Model();
        }

        $model->fill([
            'ip' => $ip,
            'serial_number' => $message[0],
            'model' => $message[1],
            'version' => $message[2],
            'imei' => $message[4],
            'imsi' => $message[5],
            'phone_number' => $message[6],
            'signal_quality' => $message[7],
            'last_hash' => $message[8] ?? 0,
            'connected_at' => now(),
            'last_communicated_at' => now(),
        ]);
        $model->save();

        return $model;
    }
}
