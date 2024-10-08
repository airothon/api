<?php

namespace App\Console\Commands;

use App\Models\Log\DeviceLog;
use App\Models\System\Device;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use Socket;

class TelnetServer extends Command
{
    protected $signature = 'telnet:server';
    protected $description = 'Telnet Server';

    protected string $address = '0.0.0.0';
    protected string $port = '7058';
    protected Socket|false $server;
    protected Collection $clients;
    protected Collection $deviceClients;

    public function handle(): void
    {
        $this->clients = collect();
        $this->deviceClients = collect();

        $this->info('Telnet Server is starting...');
        Device::query()->update(['last_communicated_at' => null]);

        /* Turn on implicit output flushing, so we see what we're getting as it comes in. */
        ob_implicit_flush();

        $this->server = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        socket_set_option($this->server, SOL_SOCKET, SO_REUSEADDR, 1);

        try {
            if (($this->server) === false) {
                echo "socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n";
                $this->restart();
            }

            if (socket_bind($this->server, $this->address, config('airothon.server.port', 7058)) === false) {
                echo "socket_bind() failed: reason: " . socket_strerror(socket_last_error($this->server)) . "\n";
                $this->restart();
            }

            if (socket_listen($this->server, 5) === false) {
                echo "socket_listen() failed: reason: " . socket_strerror(socket_last_error($this->server)) . "\n";
                $this->restart();
            }

            $this->do();
        } catch (\Exception $e) {
            echo "socket_listen() failed: reason: " . $e->getCode() . ':' . $e->getLine() . " " . $e->getMessage() . "\n";
        } finally {
            $this->restart();
        }
    }

    protected function shouldQuit(): void
    {
        if (cache()->driver('redis')->has('telnet:server:restart')) {
            cache()->driver('redis')->delete('telnet:server:restart');
            $this->restart(true);
        }
    }

    public function do(): void
    {
        do {
            $this->shouldQuit();

            $read = [];
            $read[] = $this->server;

            $read = array_merge($read, array_merge($this->clients->toArray(), $this->deviceClients->pluck('client')->toArray()));

            $write = NULL;
            $except = NULL;

            foreach ($this->deviceClients as $key => $deviceClient) {
                $device = $deviceClient['device'];

                if ($message = cache()->driver('redis')->get('telnet:server:device.' . $device->id . ':sendMessage' . $device->id)) {
                    cache()->driver('redis')->delete('telnet:server:device.' . $device->id . ':sendMessage' . $device->id);
                    $this->response($deviceClient['client'], $message, $device);

                    $device->last_communicated_at = now();
                    $device->save();
                }
            }

            // Set up a blocking call to socket_select
            if (socket_select($read, $write, $except, $tv_sec = 5) < 1) {
                continue;
            }

            // Handle new Connections
            if (in_array($this->server, $read)) {
                if (($msgsock = socket_accept($this->server)) === false) {
                    echo "socket_accept() failed: reason: " . socket_strerror(socket_last_error($this->server)) . "\n";
                    break;
                }

                $peer_address = [];
                socket_getpeername($msgsock, $peer_address['ip'], $peer_address['port']);
                echo "IP Adresi: " . $peer_address['ip'] . "\n";

                $this->clients[] = $msgsock;
            }

            foreach ($this->deviceClients as $key => $deviceClient) {
                $client = $deviceClient['client'];
                $device = $deviceClient['device'];

                try {
                    $buf = $this->readSocket($client);
                } catch (\Exception $e) {
                    unset($this->deviceClients[$key]);
                    echo "socket_read() başarısız oldu: Sebep: " . $e->getCode() . " " . $e->getMessage() . "\n";
                    break;
                }

                $device->last_communicated_at = now();
                $device->save();

                if (!$buf = trim($buf)) {
                    continue;
                }

                $message = Str::of($buf);
                $this->request($message, $device);

                if ($message->startsWith('{') or $message->endsWith('}')) {
                    $this->response($client, '#OK$', $device['device']);
                    continue;
                }

                if ($message->value() === '#OK$' or $message->startsWith('#NOK|')) {
                    continue;
                }

                $response = app('airothon')->message()->parse($message, $peer_address['ip']);
                if ($response) {
                    $this->response($client, $response->context(), $device['device']);
                }
            }


            foreach ($this->clients as $key => $client) { // for each client
                try {
                    $buf = $this->readSocket($client);
                } catch (\Exception $e) {
                    unset($this->clients[$key]);
                    echo "socket_read() başarısız oldu: Sebep: " . $e->getCode() . " " . $e->getMessage() . "\n";
                    break;
                }

                if (!$buf = trim($buf)) {
                    continue;
                }

                $message = Str::of($buf);
                $this->request($message);

                if ($message->value() === '#OK$' or $message->startsWith('#NOK|')) {
                    continue;
                }

                if (!$message->startsWith('#') or !$message->contains('$')) {
                    $this->response($client, 'NOK|Hatali Istek');
                    continue;
                }

                if (!$message->startsWith('#FC|')) {
                    $this->response($client, 'NOK|Ilk komutunuz FC olmalidir.');
                    continue;
                }

                try {
                    $device = app('airothon')->message()->parse($message, $peer_address['ip']);
                    $this->deviceClients[$device->id] = [
                        'client' => $client,
                        'device' => $device,
                    ];
                    unset($this->clients[$key]);

                    $this->request($message, $device, false);
                    $this->response($client, app('airothon')->message()->get('TS')->context(), $device);
                } catch (\Exception $e) {
                    $this->response($client, 'NOK|' . $e->getMessage());
                }
            }
        } while (true);
    }

    function readSocket(Socket $socket): string
    {
        $buf = socket_read($socket, 2048, PHP_NORMAL_READ);
        if ($buf === false) {
            throw new \Exception(socket_strerror(socket_last_error($socket)), socket_last_error($socket));
        }

        return $buf;
    }

    function request(Stringable $message, Device $device = null, bool $isLogging = true): void
    {
        if ($isLogging) {
            $this->warn('--> ' . '[' . $message . ']', 'incoming');
        }

        if ($device !== null) {
            DeviceLog::create([
                'mac' => $device->mac,
                'val1' => $message->value()[0],
                'val2' => $message->value()[0],
                'val3' => $message->value()[0],
            ]);
        }
    }

    function response(Socket $socket, string $response, Device $device = null): void
    {
        $this->info('<-- ' . '[' . $response . ']', 'outgoing');

        $response .= "\r\n";
        socket_write($socket, $response, strlen($response));
    }

    private function restart(bool $isReboot = false): void
    {
        $this->info('Telnet Server is restarting...');

        socket_close($this->server);

        $this->clients = collect();
        $this->deviceClients = collect();
        sleep(1);

        if ($isReboot) {
            die();
        }

        $this->handle();
    }

    public function line($string, $style = null, $verbosity = null)
    {
        $styled = $style ? "<$style>$string</$style>" : $string;

        $this->output->writeln($styled, $this->parseVerbosity($verbosity));
    }
}
